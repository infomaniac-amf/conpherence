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
            'speakers' => $this->getSpeakers()
        );
    }

    public function getSpeakers()
    {
        /**
         * SELECT sp.* FROM `Event` e
        INNER JOIN `EventSession` es ON es.eventId = e.id
        INNER JOIN `Session` s ON s.speakerId = es.sessionId
        INNER JOIN `Speaker` sp ON s.speakerId = sp.id
        GROUP BY sp.id
         */

        $query = $this->getEntityManager()->createQuery(<<<DQL
SELECT sp FROM Conpherence\Entities\Speaker sp
INNER JOIN sp.sessions s
INNER JOIN s.events e
GROUP BY sp.id
DQL
);
        $speakers = $query->execute();
        return $speakers;

    }
}