<?php
    namespace Conpherence\Doctrine2;

    use MwbExporter\Formatter\Doctrine2\Annotation\Formatter as Doctrine2AnnotationFormatter;
    use MwbExporter\Model\Base;

    class Formatter extends Doctrine2AnnotationFormatter
    {
        const CFG_EXTENDS = 'extends';
        const CFG_BASE_ENTITIES = 'base-entities';
        const CFG_TABLES_RESTRICT = 'tables-restrict';

        protected function init()
        {
            parent::init();
            $this->addConfigurations(array(
                static::CFG_EXTENDS => 'BaseEntity',
                static::CFG_BASE_ENTITIES => false,
                static::CFG_TABLES_RESTRICT => array(),
            ));
        }

        public function createTable(Base $parent, $node)
        {
            return new Table($parent, $node);
        }

        public function createColumn(Base $parent, $node)
        {
            return new Column($parent, $node);
        }
    }