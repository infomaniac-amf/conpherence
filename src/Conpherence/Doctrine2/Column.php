<?php
namespace Conpherence\Doctrine2;

use Doctrine\Common\Inflector\Inflector;
use MwbExporter\Formatter\Doctrine2\Annotation\Model\Column as Doctrine2AnnotationColumn;
use MwbExporter\Writer\WriterInterface;

class Column extends Doctrine2AnnotationColumn
{
    private $relatedNameString;

    public $relatedNameOverride;

    protected function init()
    {
        parent::init();

        $this->relatedNameString = $this->formatRelatedName($this->getColumnName());
    }

    /**
     * @return mixed
     */
    public function getRelatedNameString()
    {
        return $this->relatedNameString;
    }

    public function write(WriterInterface $writer)
    {
        return parent::write($writer);
    }

    public function writeGetterAndSetter(WriterInterface $writer)
    {
        $this->writeSetter($writer);
        $this->writeGetter($writer);

        return $this;
    }

    private function writeSetter(WriterInterface $writer)
    {
        $table      = $this->getTable();
        $converter  = $this->getDocument()->getFormatter()->getDatatypeConverter();
        $nativeType = $converter->getNativeType($converter->getMappedType($this));

        if($nativeType == 'datetime') {
            $nativeType = 'DateTime';
        }

        $writer
            ->write('/**')
            ->write(' * Set the value of ' . $this->getColumnName() . '.')
            ->write(' *')
            ->write(' * @param ' . $nativeType . ' $' . $this->getColumnName())
            ->write(' * @return ' . $table->getNamespace())
            ->write(' */')
            ->write('public function set' . $this->columnNameBeautifier($this->getColumnName()) . '($' . $this->getColumnName() . ' = null)')
            ->write('{')
            ->indent()
            ->write('$this->' . $this->getColumnName() . ' = $' . $this->getColumnName() . ';')
            ->write('')
            ->write('return $this;')
            ->outdent()
            ->write('}')
            ->write('');

        return $this;
    }

    private function writeGetter(WriterInterface $writer)
    {
        $table      = $this->getTable();
        $converter  = $this->getDocument()->getFormatter()->getDatatypeConverter();
        $nativeType = $converter->getNativeType($converter->getMappedType($this));
        $writer
            ->write('/**')
            ->write(' * Get the value of ' . $this->getColumnName() . '.')
            ->write(' *')
            ->write(' * @return ' . $nativeType)
            ->write(' */')
            ->write('public function get' . $this->columnNameBeautifier($this->getColumnName()) . '()')
            ->write('{')
            ->indent()
            ->write('return $this->' . $this->getColumnName() . ';')
            ->outdent()
            ->write('}')
            ->write('');

        return $this;
    }

    public function writeRelations(WriterInterface $writer)
    {
        $formatter = $this->getDocument()->getFormatter();
        // one to many references
        foreach ($this->foreigns as $foreign) {
            if ($foreign->getForeign()->getTable()->isManyToMany()) {
                // do not create entities for many2many tables
                continue;
            }
            if ($foreign->parseComment('unidirectional') === 'true') {
                // do not output mapping in foreign table when the unidirectional option is set
                continue;
            }

            $targetEntity = $foreign->getOwningTable()->getModelName();
            $targetEntityFQCN = $foreign->getOwningTable()->getModelNameAsFQCN($foreign->getReferencedTable()->getEntityNamespace());
//            $mappedBy = $foreign->getReferencedTable()->getModelName();
            $mappedBy = lcfirst($this->removeFKNaming($foreign->getForeign()->getColumnName()));
            //$varName = $this->removeFKNaming($this->local->getForeign()->getColumnName());//lcfirst($targetEntity);

            $annotationOptions = array(
                'targetEntity' => $targetEntityFQCN,
                'mappedBy' => lcfirst($mappedBy),
                'cascade' => array('persist'),
                'fetch' => $formatter->getFetchOption($foreign->parseComment('fetch')),
                'orphanRemoval' => $formatter->getBooleanOption($foreign->parseComment('orphanRemoval')),
            );

            $joinColumnAnnotationOptions = array(
                'name' => $foreign->getForeign()->getColumnName(),
                'referencedColumnName' => $foreign->getLocal()->getColumnName(),
                'onDelete' => $formatter->getDeleteRule($foreign->getLocal()->getParameters()->get('deleteRule')),
                'nullable' => !$foreign->getForeign()->isNotNull() ? NULL : false,
            );

            if($mappedBy == $this->getTable()->getModelName())
                unset($joinColumnAnnotationOptions['name']);

            //check for OneToOne or OneToMany relationship
            if ($foreign->isManyToOne()) { // is OneToMany
                $related = $this->getRelatedName($foreign);
                $varName = lcfirst(Inflector::pluralize($targetEntity));
//                if(!empty($related))
//                    $varName = $related;

                $relatedNameOverride = $foreign->parseComment('relName');

                $orderBy = $foreign->parseComment('orderBy');
                $orderByOptions = null;
                if(!empty($orderBy)) {
                    $orderByOptions = $this->parseOrderByComment($orderBy);
                }

                if (!empty($relatedNameOverride)) {
                    $varName = $relatedNameOverride;
                    $this->relatedNameOverride = $varName;
                }

                if(!$this->getTable()->hasProperty($varName)) {
                    $this->getTable()->addProperty($varName);

                    // flip name and remove referencedColumnName
                    if($foreign->getOwningTable()->getModelName() != $this->getTable()->getModelName())
                    {
                        $joinColumnAnnotationOptions['name'] = $joinColumnAnnotationOptions['referencedColumnName'];
                        unset($joinColumnAnnotationOptions['referencedColumnName']);
                    }

                    $writer
                        ->write('/**')
                        ->write(' * '.$this->getTable()->getAnnotation('OneToMany', $annotationOptions))
                        ->write(' * '.$this->getTable()->getAnnotation('JoinColumn', $joinColumnAnnotationOptions));

                    if(!empty($orderByOptions)) {
                        $writer->write(' * '.$this->getTable()->getAnnotation('OrderBy', $orderByOptions));
                    }

                    $writer->write(' */')
                        ->write('protected $'.$varName.';')
                        ->write('');
                }
            } else { // is OneToOne
                $selfReferential = $targetEntity == $mappedBy;
                $varName = lcfirst($targetEntity);

                if(!$this->getTable()->hasProperty($varName)) {
                    $this->getTable()->addProperty($varName);

                    // flip name and remove referencedColumnName
                    if($foreign->getOwningTable()->getModelName() != $this->getTable()->getModelName())
                    {
                        $joinColumnAnnotationOptions['name'] = $joinColumnAnnotationOptions['referencedColumnName'];
                        unset($joinColumnAnnotationOptions['referencedColumnName']);
                    }

                    if(!$selfReferential) {
                        $writer
                            ->write('/**')
                            ->write(' * '.$this->getTable()->getAnnotation('OneToOne', $annotationOptions))
                            ->write(' * '.$this->getTable()->getAnnotation('JoinColumn', $joinColumnAnnotationOptions));

                        if(!empty($orderByOptions)) {
                            $writer->write(' * '.$this->getTable()->getAnnotation('OrderBy', $orderByOptions));
                        }

                        $writer
                            ->write(' */')
                            ->write('protected $'.lcfirst($targetEntity).';')
                            ->write('');
                    }
                }
            }
        }
        // many to references
        if (NULL !== $this->local) {
            $targetEntity = $this->local->getReferencedTable()->getModelName();
            $targetEntityFQCN = $this->local->getReferencedTable()->getModelNameAsFQCN($this->local->getOwningTable()->getEntityNamespace());
            $m2mRelations = $this->local->getReferencedTable()->getManyToManyRelations();
            $inversedBy = $this->local->getOwningTable()->getModelName();

            if ($this->local->getForeign()->parseComment('targetEntity')) {
                $targetEntityFQCN = $this->local->getForeign()->parseComment('targetEntity');
            }

            if(!empty($m2mRelations[$inversedBy]['reference'])) {
                $reference = $m2mRelations[$inversedBy]['reference'];
                $inversedBy = Inflector::pluralize($this->removeFKNaming($reference->getLocal()->relatedNameOverride));
            }

            $annotationOptions = array(
                'targetEntity' => $targetEntityFQCN,
                'mappedBy' => NULL,
                'inversedBy' => $inversedBy,
                'cascade' => array('persist'),
                // 'fetch' => $formatter->getFetchOption($this->local->parseComment('fetch')),
                // 'orphanRemoval' => $formatter->getBooleanOption($this->local->parseComment('orphanRemoval')),
            );
            $joinColumnAnnotationOptions = array(
                'name' => $this->local->getForeign()->getColumnName(),
                'referencedColumnName' => $this->local->getLocal()->getColumnName(),
                'onDelete' => $formatter->getDeleteRule($this->local->getParameters()->get('deleteRule')),
                'nullable' => !$this->local->getForeign()->isNotNull() ? NULL : false,
            );

//            if($this->local->getForeign()->getTable() == $this->getTable())
//                unset($joinColumnAnnotationOptions['name']);

            //check for OneToOne or ManyToOne relationship
            if ($this->local->isManyToOne()) { // is ManyToOne

                $related = null;

                if ($this->local->getOwningTable()->isManyToMany()) {
                    return $this;
                } else {
                    $related    = $this->getManyToManyRelatedName($this->local->getReferencedTable()->getRawTableName(),
                        $this->local->getForeign()->getColumnName()
                    );
                    $refRelated = $this->local->getLocal()->getRelatedName($this->local);
                }

                if ($this->local->parseComment('unidirectional') === 'true') {
                    $annotationOptions['inversedBy'] = NULL;
                } else {
                    $annotationOptions['inversedBy'] = $related ?: lcfirst(Inflector::pluralize($annotationOptions['inversedBy']));
                }

                $varName = $this->removeFKNaming($this->local->getForeign()->getColumnName());//lcfirst($targetEntity);

                if(!$this->getTable()->hasProperty($varName)) {
                    $this->getTable()->addProperty($varName);
                    $writer
                        ->write('/**')
                        ->write(' * '.$this->getTable()->getAnnotation('ManyToOne', $annotationOptions))
                        ->write(' * '.$this->getTable()->getAnnotation('JoinColumn', $joinColumnAnnotationOptions))
                        ->write(' */')
                        ->write('protected $'.$varName.';')
                        ->write('')
                    ;
                }
            } else { // is OneToOne
                $selfReferential = $targetEntity == $inversedBy;

                if ($this->local->parseComment('unidirectional') === 'true') {
                    $annotationOptions['inversedBy'] = NULL;
                } else {
                    $annotationOptions['inversedBy'] = lcfirst($annotationOptions['inversedBy']);
                }

                if($selfReferential);
//                    unset($annotationOptions['inversedBy']);

                $annotationOptions['cascade'] = array('persist');//$formatter->getCascadeOption($this->local->parseComment('cascade'));
                $varName = $this->removeFKNaming($this->local->getForeign()->getColumnName());//lcfirst($targetEntity);

                // flip name and referencedColumnName
                if($this->local->getOwningTable()->getModelName() != $this->getTable()->getModelName())
                {
                    $oldReferencedColumnName = $joinColumnAnnotationOptions['referencedColumnName'];
                    $joinColumnAnnotationOptions['referencedColumnName'] = $joinColumnAnnotationOptions['name'];
                    $joinColumnAnnotationOptions['name'] = $oldReferencedColumnName;
                }

                if(!$this->getTable()->hasProperty($varName)) {
                    $this->getTable()->addProperty($varName);
                    $writer
                        ->write('/**')
    //                        ->writeIf($isComposite, ' * @Id')
                        ->write(' * '.$this->getTable()->getAnnotation('OneToOne', $annotationOptions))
                        ->write(' * '.$this->getTable()->getAnnotation('JoinColumn', $joinColumnAnnotationOptions))
                        ->write(' */')
                        ->write('protected $'.$varName.';')
                        ->write('')
                    ;
                }
            }
        }

        return $this;
    }

    /**
     * Format column name as relation to foreign table.
     *
     * @param string $column  The column name
     * @param bool   $code    If true, use result as PHP code or false, use as comment
     * @return string
     */
    public function formatRelatedName($column, $code = true)
    {
        $column = $this->removeFKNaming($column);
        return $code ? sprintf(lcfirst($this->columnNameBeautifier($column))) : sprintf('related by `%s`', $column);
    }

    public function writeRelationsGetterAndSetter(WriterInterface $writer)
    {
        $table = $this->getTable();
        // one to many references
        foreach ($this->foreigns as $foreign) {
            if ($foreign->getForeign()->getTable()->isManyToMany()) {
                // do not create entities for many2many tables
                continue;
            }
            if ($foreign->parseComment('unidirectional') === 'true') {
                // do not output mapping in foreign table when the unidirectional option is set
                continue;
            }

            if ($foreign->isManyToOne()) { // is ManyToOne
                $foreignRelatedName = $foreign->getForeign()->getRelatedNameString();

                $relatedName = ucfirst($this->getRelatedName($foreign));
                $relatedText = $this->getRelatedName($foreign, false);

                $relatedNameOverride = $foreign->parseComment('relName');
                if (!empty($relatedNameOverride)) {
                    $relatedName = $relatedNameOverride;
                }

                if (empty($relatedName)) {
//                    $relatedName = $this->columnNameBeautifier($foreign->getOwningTable()->getModelName());
                    $relatedName = $foreign->getOwningTable()->getModelName();
                }

                $relatedNameSingular = Inflector::singularize($relatedName);

                $writer
                    // setter
                    ->write('/**')
                    ->write(' * Add ' . trim($relatedName . ' ' . $relatedText) . ' entity to collection (one to many).')
                    ->write(' *')
                    ->write(' * @param ' . $foreign->getOwningTable()->getNamespace() . ' $' . lcfirst($relatedNameSingular))
                    ->write(' * @return ' . $table->getNamespace())
                    ->write(' */')
                    ->write('public function add' . ucfirst($relatedNameSingular) . '(' . $foreign->getOwningTable()->getModelNameAsFQCN(NULL, true) . ' $' . lcfirst($relatedNameSingular) . ' = null)')
                    ->write('{')
                    ->indent()
                        ->write('if($'.lcfirst($relatedNameSingular).') {')
                        ->indent()
                            ->write('$' . lcfirst($relatedNameSingular).'->set'. ucfirst($foreignRelatedName) . '($this);')
                        ->outdent()
                        ->write('}')
                        ->write('')
                        ->write('$this->' . lcfirst(Inflector::pluralize($relatedName)) . '[] = $' . lcfirst($relatedNameSingular) . ';')
                        ->write('')
                        ->write('return $this;')
                    ->outdent()
                    ->write('}')
                    ->write('')
                    // getter
                    ->write('/**')
                    ->write(' * Get ' . trim($relatedName . ' ' . $relatedText) . ' entity collection (one to many).')
                    ->write(' *')
                    ->write(' * @return ' . $table->getCollectionInterface())
                    ->write(' */')
                    ->write('public function get' . ucfirst(Inflector::pluralize($relatedName)) . '()')
                    ->write('{')
                    ->indent()
                    ->write('return $this->' . lcfirst(Inflector::pluralize($relatedName)) . ';')
                    ->outdent()
                    ->write('}')
                    ->write('')
                    // write remove
                    ->write('/**')
                    ->write(' * Remove ' . trim($relatedName . ' ' . $relatedText) . ' entity from collection (one to many).')
                    ->write(' *')
                    ->write(' * @param ' . $foreign->getOwningTable()->getNamespace() . ' $' . lcfirst($relatedNameSingular))
                    ->write(' * @param bool $delete')
                    ->write(' * @return ' . $table->getNamespace())
                    ->write(' */')
                    ->write('public function remove' . ucfirst($relatedNameSingular) . '(' . $foreign->getOwningTable()->getModelNameAsFQCN(NULL, true) . ' $' . lcfirst($relatedNameSingular) . ' = null, $delete=false)')
                    ->write('{')
                    ->indent()
                        ->write('if($'.lcfirst($relatedNameSingular).') {')
                            ->indent()
                            ->write('$this->%s->removeElement($%s);', lcfirst(Inflector::pluralize($relatedName)), lcfirst($relatedNameSingular))
                            ->write('if ($delete) {')
                                ->indent()
                                    ->write('$this->getEntityManager()->remove($%s);', lcfirst($relatedNameSingular))
                                ->outdent()
                            ->write('}')
                            ->outdent()
                        ->write('}')
                        ->write('')
                        ->write('return $this;')
                    ->outdent()
                    ->write('}');
            } else { // OneToOne
                $targetEntity  = $foreign->getReferencedTable()->getModelName();
                $relatedEntity = $foreign->getOwningTable()->getModelName();

                $relatedName      = $this->getRelatedName($foreign);
                $relatedText = $this->getRelatedName($foreign, false);

                $selfReferential = $targetEntity == $relatedEntity;

                if(!$selfReferential) {
                    $writer
                        // setter
                        ->write('/**')
                        ->write(' * Set ' . $foreign->getOwningTable()->getModelName() . ' entity (one to one).')
                        ->write(' *')
                        ->write(' * @param ' . $foreign->getOwningTable()->getNamespace() . ' $' . lcfirst($foreign->getOwningTable()->getModelName()))
                        ->write(' * @return ' . $table->getNamespace())
                        ->write(' */')
                        ->write('public function set' . $this->columnNameBeautifier($foreign->getOwningTable()->getModelName()) . '(' . $foreign->getOwningTable()->getModelNameAsFQCN(NULL, true) . ' $' . lcfirst($foreign->getOwningTable()->getModelName()) . ' = null)')
                        ->write('{')
                        ->indent()
                        ->write('$this->' . lcfirst($foreign->getOwningTable()->getModelName()) . ' = $' . lcfirst($foreign->getOwningTable()->getModelName()) . ';')
                        ->write('')
                        ->write('return $this;')
                        ->outdent()
                        ->write('}')
                        ->write('')
                        // getter
                        ->write('/**')
                        ->write(' * Get ' . $foreign->getOwningTable()->getModelName() . ' entity (one to one).')
                        ->write(' *')
                        ->write(' * @return ' . $foreign->getOwningTable()->getNamespace())
                        ->write(' */')
                        ->write('public function get' . $this->columnNameBeautifier($foreign->getOwningTable()->getModelName()) . '()')
                        ->write('{')
                        ->indent()
                        ->write('return $this->' . lcfirst($foreign->getOwningTable()->getModelName()) . ';')
                        ->outdent()
                        ->write('}');
                }
            }
            $writer
                ->write('');
        }
        // many to one references
        if (NULL !== $this->local) {
            $unidirectional = ($this->local->parseComment('unidirectional') === 'true');

            if ($this->local->isManyToOne()) { // is ManyToOne
                $relatedName  = $this->formatRelatedName($this->local->getForeign()->getColumnName());
                $relatedText  = $this->getManyToManyRelatedName($this->local->getReferencedTable()->getRawTableName(), $this->local->getForeign()->getColumnName(), false);

                if (empty($relatedName)) {
                    $relatedName = lcfirst($this->local->getReferencedTable()->getModelName());
                }

                $writer
                    // setter
                    ->write('/**')
                    ->write(' * Set ' . trim($this->local->getReferencedTable()->getModelName() . ' ' . $relatedText) . ' entity (many to one).')
                    ->write(' *')
                    ->write(' * @param ' . $this->local->getReferencedTable()->getNamespace() . ' $' . lcfirst($this->local->getReferencedTable()->getModelName()))
                    ->write(' * @return ' . $table->getNamespace())
                    ->write(' */')
                    ->write('public function set' . ucfirst($relatedName) . '(' . $this->local->getReferencedTable()->getModelNameAsFQCN(NULL, true) . ' $' . lcfirst($this->local->getReferencedTable()->getModelName()) . ' = null)')
                    ->write('{')
                    ->indent()
                    ->write('$this->' . $relatedName . ' = $' . lcfirst($this->local->getReferencedTable()->getModelName()) . ';')
                    ->write('')
                    ->write('return $this;')
                    ->outdent()
                    ->write('}')
                    ->write('')
                    // getter
                    ->write('/**')
                    ->write(' * Get ' . trim($this->local->getReferencedTable()->getModelName() . ' ' . $relatedText) . ' entity (many to one).')
                    ->write(' *')
                    ->write(' * @return ' . $this->local->getReferencedTable()->getNamespace())
                    ->write(' */')
                    ->write('public function get' . ucfirst($relatedName) . '()')
                    ->write('{')
                    ->indent()
                    ->write('return $this->' . $relatedName . ';')
                    ->outdent()
                    ->write('}')
                    ->write('');
            } else { // OneToOne
                $local = $this->local;
                $varName = lcfirst($this->removeFKNaming($local->getForeign()->getColumnName()));

                $writer
                    // setter
                    ->write('/**')
                    ->write(' * Set ' . $this->local->getReferencedTable()->getModelName() . ' entity (one to one).')
                    ->write(' *')
                    ->write(' * @param ' . $this->local->getReferencedTable()->getNamespace() . ' $' . $varName)
                    ->write(' * @return ' . $table->getNamespace())
                    ->write(' */')
                    ->write('public function set' . ucfirst($varName) . '(' . $this->local->getReferencedTable()->getModelNameAsFQCN(NULL, true) . ' $' . $varName . ' = null)')
                    ->write('{')
                    ->indent()
                    ->writeCallback(function(WriterInterface $writer, Column $_this = NULL) use ($unidirectional, $local, $varName)
                        {
                            $targetEntity = $local->getReferencedTable()->getModelName();
                            $relatedEntity = $local->getOwningTable()->getModelName();
                            $selfReferential = $targetEntity == $relatedEntity;

                            // do not have these integrity setters if self-referential - causes endless loop
                            if($selfReferential)
                                return;

                            if(!$unidirectional && isset($local))
                            {
                                $writer->write('if($' . $varName.') {')
                                    ->indent()
                                        ->write('$' . $varName . '->set' . $_this->columnNameBeautifier($local->getOwningTable()->getModelName()) . '($this);')
                                    ->outdent()
                                    ->write('}')
                                    ->write('');
                            }
                        })
                    ->write('$this->' . $varName . ' = $' . $varName . ';')
                    ->write('')
                    ->write('return $this;')
                    ->outdent()
                    ->write('}')
                    ->write('')
                    // getter
                    ->write('/**')
                    ->write(' * Get ' . $this->local->getReferencedTable()->getModelName() . ' entity (one to one).')
                    ->write(' *')
                    ->write(' * @return ' . $this->local->getReferencedTable()->getNamespace())
                    ->write(' */')
                    ->write('public function get' . ucfirst($varName) . '()')
                    ->write('{')
                    ->indent()
                    ->write('return $this->' . lcfirst($varName) . ';')
                    ->outdent()
                    ->write('}')
                    ->write('');
            }
        }

        return $this;
    }

    public function getManyRelations()
    {
        $table = $this->getTable();
        $relations = array();
        // one to many references
        foreach ($this->foreigns as $foreign) {
            if ($foreign->getForeign()->getTable()->isManyToMany()) {
                // do not create entities for many2many tables
                continue;
            }
            if ($foreign->parseComment('unidirectional') === 'true') {
                // do not output mapping in foreign table when the unidirectional option is set
                continue;
            }

            if ($foreign->isManyToOne()) { // is ManyToOne
                $relations[] = lcfirst(Inflector::pluralize($foreign->getOwningTable()->getModelName()));
            }
        }

        return $relations;
    }

    public function writeArrayCollection(WriterInterface $writer)
    {
        $properties = array();

        foreach ($this->foreigns as $foreign) {
            $varName = lcfirst(Inflector::pluralize($foreign->getOwningTable()->getModelName()));
            if(in_array($varName, $properties))
                continue;

            if ($foreign->getForeign()->getTable()->isManyToMany()) {
                // do not create entities for many2many tables
                continue;
            }

            if ($foreign->isManyToOne() && $foreign->parseComment('unidirectional') !== 'true') { // is ManyToOne
                $related = $this->getRelatedName($foreign);
                $relatedNameOverride = $foreign->parseComment('relName');
                if(!empty($relatedNameOverride))
                    $varName = $relatedNameOverride;

                $writer->write('$this->%s = new %s();', $varName, $this->getTable()->getCollectionClass(false));
                $properties[] = $varName;
            }
        }

        return $this;
    }

    public function columnNameBeautifier($columnName)
    {
        return parent::columnNameBeautifier($columnName);
    }

    public function removeFKNaming($columnName)
    {
        $columnName = str_replace('_id', '', $columnName);
        $columnName = str_replace('_ID', '', $columnName);
        $columnName = str_replace('Id', '', $columnName);
        $columnName = str_replace('ID', '', $columnName);
        return $columnName;
    }

    /**
     * @return \MwbExporter\Model\ForeignKey
     */
    public function getForeigns()
    {
        return $this->foreigns;
    }

    /**
     * Get the @OrderBy definition by comment
     *
     * eg: {d:orderBy}weight:ASC{/d:orderBy}
     *
     * @param $orderBy
     * @return array|null
     */
    private function parseOrderByComment($orderBy)
    {
        if(empty($orderBy)) {
            return null;
        }

        $order = 'ASC';
        preg_match("/([a-z0-9]+)(:(.+))?/ui", $orderBy, $matches);

        if(!isset($matches[1])) return null;

        $field = $matches[1];
        $order = isset($matches[3]) ? $matches[3] : $order;
        return ['value' => [$field => $order]];
    }
}