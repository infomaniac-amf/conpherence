<?php

namespace Conpherence\Entities;

use Conpherence\Entities\Base\BaseSession;

/**
 * @Entity
 * @HasLifecycleCallbacks
 */
class Session extends BaseSession
{
    public function getDescription()
    {
        return str_replace(PHP_EOL, '<br>', parent::getDescription());
    }

    /**
     * Return an associative array of class properties
     *
     * @return array
     */
    public function export()
    {
        return array(
            'id'          => $this->getId(),
            'date'        => $this->getDate()->format('Y-m-d H:i:s'),
            'title'       => $this->getTitle(),
            'description' => $this->getDescription(),
            'speaker'     => $this->getSpeaker(),
            'event'       => $this->getEvents()->toArray()
        );
    }
}