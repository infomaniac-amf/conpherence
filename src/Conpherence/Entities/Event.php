<?php

namespace Conpherence\Entities;

use Conpherence\Entities\Base\BaseEvent;

/**
 * @Entity
 * @HasLifecycleCallbacks
 */
class Event extends BaseEvent
{
    /**
     * Return an associative array of class properties
     *
     * @return array
     */
    public function export()
    {
        return array(
            'id' => $this->getId(),
            'name' => $this->getName(),
            'description' => $this->getDescription(),
            'hashtag' => $this->getHashtag(),
            'url' => $this->getUrl(),
            'sessions' => $this->getSessions()->toArray()
        );
    }
}