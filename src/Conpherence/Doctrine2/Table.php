<?php
namespace Conpherence\Doctrine2;

use Doctrine\Common\Inflector\Inflector;
use MwbExporter\Formatter\Doctrine2\Annotation\Model\Table as Doctrine2AnnotationTable;
use MwbExporter\Model\Base;
use MwbExporter\Writer\WriterInterface;

class Table extends Doctrine2AnnotationTable
{
    private $properties = array();

    public function __construct(Base $parent = null, $node)
    {
        parent::__construct($parent, $node);

        Inflector::rules('plural',
            array(
                'irregular'   => array('criterion' => 'criteria')
            )
        );
    }

    public function writeTable(WriterInterface $writer)
    {
        if (!$this->isExternal()) {
            // check if table has been restricted from being generated
            $tablesRestrict = $this->getDocument()->getConfig()->get(Formatter::CFG_TABLES_RESTRICT);
            if (count($tablesRestrict) && !in_array(strtolower($this->getModelName()), $tablesRestrict)) {
                return null;
            }

            $namespace = $this->getEntityNamespace();
            if ($repositoryNamespace = $this->getDocument()->getConfig()->get(Formatter::CFG_REPOSITORY_NAMESPACE)) {
                $repositoryNamespace .= '\\';
            }
            $skipGetterAndSetter = $this->getDocument()->getConfig()->get(Formatter::CFG_SKIP_GETTER_SETTER);
            $serializableEntity  = $this->getDocument()->getConfig()->get(Formatter::CFG_GENERATE_ENTITY_SERIALIZATION);
            $lifecycleCallbacks  = $this->getLifecycleCallbacks();

            $extends      = $this->getDocument()->getConfig()->get(Formatter::CFG_EXTENDS);
            $baseEntities = $this->getDocument()->getConfig()->get(Formatter::CFG_BASE_ENTITIES);

            $comment = $this->getComment();
            $writer
                ->open($this->getTableFileName())
                ->write('<?php')
                ->write('')
                ->write('/**')
                ->write(' * Generated base entity')
                ->write(' * [DO NOT MODIFY]')
                ->write(' */')
                ->write('')
                ->write('namespace %s;', $this->getEntityNamespace(true))
                ->write('')
                ->writeCallback(function (WriterInterface $writer, Table $_this = null) {
                        $_this->writeUsedClasses($writer);
                    }
                )
                ->write('/**')
                ->write(' * ' . $this->getNamespace(null, false))
                ->write(' *')
                ->writeIf($comment, $comment)
                //                    ->write(' * ' . $this->getAnnotation('Entity', array('repositoryClass' => $this->getDocument()->getConfig()->get(Formatter::CFG_AUTOMATIC_REPOSITORY) ? $repositoryNamespace . $this->getModelName() . 'Repository' : NULL)))
                ->write(' * ' . $this->getAnnotation('MappedSuperclass'))
                ->write(' * @HasLifecycleCallbacks')
                ->write(' *')
                ->write(' * ' . $this->getAnnotation('Table',
                        array(
                            'name'              => $this->quoteIdentifier($this->getRawTableName()),
                            'uniqueConstraints' => $this->getUniqueConstraintsAnnotation()
                        )
                    )
                )
                ->write(' */')
                ->writeIf($baseEntities, 'abstract class ' . $this->getModelName(true) . ' extends ' . $extends)
                ->writeIf(!$baseEntities, 'class ' . $this->getModelName(true) . ' extends ' . $extends)
                ->write('{')
                ->indent()
                ->writeCallback(function (WriterInterface $writer, Table $_this = null) use (
                        $skipGetterAndSetter,
                        $serializableEntity,
                        $lifecycleCallbacks
                    ) {
                        $writer->write('');
                        $_this->getColumns()->write($writer);
                        $_this->writeManyToMany($writer);
                        $_this->writeConstructor($writer);
                        if (!$skipGetterAndSetter) {
                            $_this->getColumns()->writeGetterAndSetter($writer);
                            $_this->writeManyToManyGetterAndSetter($writer);
                        }
                        foreach ($lifecycleCallbacks as $callback => $handlers) {
                            foreach ($handlers as $handler) {
                                $writer
                                    ->write('/**')
                                    ->write(' * @%s', ucfirst($callback))
                                    ->write(' */')
                                    ->write('public function %s()', $handler)
                                    ->write('{')
                                    ->write('}')
                                    ->write('');
                            }
                        }
                        if ($serializableEntity) {
                            $_this->writeSerialization($writer);
                        }
                        //                    $writer->writeIf($_this->isManyToMany(), PHP_EOL."public function isM2M() { return true; }");
                    }
                )
                ->outdent()
                ->write('}')
                ->close();

            return self::WRITE_OK;
        }

        return self::WRITE_EXTERNAL;
    }

    protected function getUsedClasses()
    {
        $baseEntity = array(
            "Conpherence\\Entities",
            "Conpherence\\Entities\\Base\\BaseEntity",
            "\\DateTime",
        );

        return array_merge($baseEntity, parent::getUsedClasses());
    }

    public function getEntityNamespace($base = false)
    {
        $baseEntities = $this->getDocument()->getConfig()->get(Formatter::CFG_BASE_ENTITIES);
        return parent::getEntityNamespace() . ($baseEntities && $base ? "\\Base" : "");
    }

    public function getModelName($base = false)
    {
        $baseEntities = $this->getDocument()->getConfig()->get(Formatter::CFG_BASE_ENTITIES);
        return ($baseEntities && $base ? "Base" : null) . parent::getModelName();
    }

    public function getModelNameAsFQCN($referenceNamespace = null, $base = false)
    {
        $baseEntities = $this->getDocument()->getConfig()->get(Formatter::CFG_BASE_ENTITIES);
        $namespace    = $this->getEntityNamespace();
        $fqcn         = ($namespace == $referenceNamespace) ? true : false;

        if ($baseEntities && $base) {
            $fragments    = explode('\\', $namespace);
            $lastFragment = array_pop($fragments);

            return $lastFragment . '\\' . $this->getModelName();
        }
        return $fqcn ? $namespace . '\\' . $this->getModelName() : $this->getModelName();
    }

    public function writeManyToMany(WriterInterface $writer)
    {
        $formatter = $this->getDocument()->getFormatter();
        foreach ($this->manyToManyRelations as $mappedRelation => $relation) {
            $isOwningSide = $formatter->isOwningSide($relation, $mappedRelation);

            $annotationOptions = array(
                'targetEntity' => $relation['refTable']->getModelNameAsFQCN($this->getEntityNamespace()),
                'mappedBy'     => null,
                'inversedBy'   => lcfirst(Inflector::pluralize($this->getModelName())),
                'cascade'      => array('persist'),
                'fetch'        => $formatter->getFetchOption($relation['reference']->parseComment('fetch')),
            );

            // if this is the owning side, also output the JoinTable Annotation
            // otherwise use "mappedBy" feature
            if ($isOwningSide) {
                if ($mappedRelation->parseComment('unidirectional') === 'true') {
                    unset($annotationOptions['inversedBy']);
                }

                $writer
                    ->write('/**')
                    ->write(' * ' . $this->getAnnotation('ManyToMany', $annotationOptions))
                    ->write(' * ' . $this->getAnnotation('JoinTable',
                            array(
                                'name'               => $relation['reference']->getOwningTable()->getRawTableName(),
                                'joinColumns'        => array(
                                    $this->getJoinColumnAnnotation(
                                        $relation['reference']->getForeign()->getColumnName(),
                                        $relation['reference']->getLocal()->getColumnName(),
                                        $relation['reference']->getParameters()->get('deleteRule')
                                    )
                                ),
                                'inverseJoinColumns' => array(
                                    $this->getJoinColumnAnnotation(
                                        $mappedRelation->getForeign()->getColumnName(),
                                        $mappedRelation->getLocal()->getColumnName(),
                                        $mappedRelation->getParameters()->get('deleteRule')
                                    )
                                )
                            ),
                            array('multiline' => true, 'wrapper' => ' * %s')
                        )
                    )
                    ->write(' */');
            } else {
                if ($relation['reference']->parseComment('unidirectional') === 'true') {
                    continue;
                }

                $mappedBy = $relation['reference']->getForeign()->formatRelatedName($relation['reference']->getForeign(
                    )->getColumnName()
                );
                $mappedBy = lcfirst(Inflector::pluralize($mappedBy));

                $annotationOptions['mappedBy']   = $mappedBy;
                $annotationOptions['inversedBy'] = null;
                $writer
                    ->write('/**')
                    ->write(' * ' . $this->getAnnotation('ManyToMany', $annotationOptions))
                    ->write(' */');
            }

            $varName = $relation['reference']->getForeign()->formatRelatedName($mappedRelation->getForeign(
                )->getColumnName()
            );
            $varName = lcfirst(Inflector::pluralize($varName));
            $writer
                ->write('protected $' . $varName . ';')
                ->write('');
        }

        return $this;
    }

    public function writeConstructor(WriterInterface $writer)
    {
        $formatter = $this->getDocument()->getFormatter();
        $writer
            ->write('public function __construct()')
            ->write('{')
            ->indent()
            ->writeCallback(function (WriterInterface $writer, Table $_this = null) use ($formatter) {
                    $_this->getColumns()->writeArrayCollections($writer);
                    foreach ($_this->getManyToManyRelations() as $mappedRelation => $relation) {
                        $isOwningSide = $formatter->isOwningSide($relation, $mappedRelation);

                        $varName = $relation['reference']->getForeign()->formatRelatedName($mappedRelation->getForeign(
                            )->getColumnName()
                        );
                        $varName = lcfirst(Inflector::pluralize($varName));
                        $writer->write('$this->%s = new %s();', $varName, $_this->getCollectionClass(false));
                    }
                }
            )
            ->outdent()
            ->write('}')
            ->write('');

        return $this;
    }

    public function writeManyToManyGetterAndSetter(WriterInterface $writer)
    {
        $formatter = $this->getDocument()->getFormatter();
        foreach ($this->manyToManyRelations as $mappedRelation => $relation) {
            $isOwningSide = $formatter->isOwningSide($relation, $mappedRelation);
            $varName      = $relation['reference']->getForeign()->formatRelatedName($mappedRelation->getForeign(
                )->getColumnName()
            );
            $singular     = ucfirst(Inflector::singularize($varName));
            $plural       = lcfirst(Inflector::pluralize($varName));

            $writer
                ->write('/**')
                ->write(' * Add ' . $relation['refTable']->getModelName() . ' entity to collection.')
                ->write(' *')
                ->write(' * @param ' . $relation['refTable']->getNamespace() . ' $' . lcfirst($singular))
                ->write(' * @return ' . $this->getNamespace($this->getModelName()))
                ->write(' */')
                ->write('public function add' . $singular . '(' . $relation['refTable']->getModelNameAsFQCN(null,
                        true
                    ) . ' $' . lcfirst($singular) . ' = null)'
                )
                ->write('{')
                ->indent()
                ->writeCallback(function (WriterInterface $writer, Table $_this = null) use (
                        $isOwningSide,
                        $relation,
                        $mappedRelation,
                        $singular,
                        $plural
                    ) {
                        if ($isOwningSide) {
                            $writer->write('$%s->add%s($this);', lcfirst($singular), $_this->getModelName());
                        }
                    }
                )
                ->write('$this->' . $plural . '[] = $' . lcfirst($singular) . ';')
                ->write('')
                ->write('return $this;')
                ->outdent()
                ->write('}')
                ->write('')
                ->write('/**')
                ->write(' * Get ' . $relation['refTable']->getModelName() . ' entity collection.')
                ->write(' *')
                ->write(' * @return ' . $this->getCollectionInterface())
                ->write(' */')
                ->write('public function get' . ucfirst($plural) . '()')
                ->write('{')
                ->indent()
                ->write('return $this->' . $plural . ';')
                ->outdent()
                ->write('}')
                ->write('')
                ->writeCallback(function (WriterInterface $writer, Table $_this = null) use (
                        $isOwningSide,
                        $relation,
                        $mappedRelation,
                        $singular,
                        $plural
                    ) {
                        if ($isOwningSide) {
                            // write remove
                            $writer->write('/**')
                                ->write(' * Remove ' . $relation['refTable']->getModelName(
                                    ) . ' entity from collection.'
                                )
                                ->write(' *')
                                ->write(' * @param ' . $relation['refTable']->getNamespace() . ' $' . lcfirst($singular)
                                )
                                ->write(' * @param bool $delete')
                                ->write(' * @return ' . $_this->getNamespace($_this->getModelName()))
                                ->write(' */')
                                ->write('')
                                ->write('public function remove' . $singular . '(' . $relation['refTable']->getModelNameAsFQCN(null,
                                        true
                                    ) . ' $' . lcfirst($singular) . ' = null, $delete=false)'
                                )
                                ->write('{')
                                ->indent()
                                ->write('if($' . lcfirst($singular) . ') {')
                                ->indent()
                                ->write('$this->%s->removeElement($%s);', $plural, lcfirst($singular))
                                ->write('if ($delete) {')
                                ->indent()
                                ->write('$this->getEntityManager()->remove($%s);', lcfirst($singular))
                                ->outdent()
                                ->write('}')
                                ->outdent()
                                ->write('}')
                                ->write('')
                                ->write('return $this;')
                                ->outdent()
                                ->write('}')
                                ->write('');
                        }
                    }
                );
        }

        return $this;
    }

    /**
     * @return array
     */
    public function getProperties()
    {
        return $this->properties;
    }

    /**
     * @param $property
     *
     * @return array
     */
    public function addProperty($property)
    {
        $this->properties[] = $property;
        return $this->properties;
    }

    /**
     * @param $property
     *
     * @return array
     */
    public function hasProperty($property)
    {
        return in_array($property, $this->properties);
    }
}