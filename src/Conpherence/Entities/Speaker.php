<?php

namespace Conpherence\Entities;

use Conpherence\Entities\Base\BaseSpeaker;
use Exception;
use Infomaniac\Type\ByteArray;

/**
 * @Entity
 * @HasLifecycleCallbacks
 */
class Speaker extends BaseSpeaker
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
            'country' => $this->getCountry(),
            'bio' => $this->getBio(),
            'sessions' => $this->getSessions()->toArray(),
            'flagIcon' => $this->getFlag()
        );
    }

    private function getFlag()
    {
        $basePath = realpath(__DIR__.'/../../../assets/flags');
        $country = str_replace(' ', '-', $this->getCountry());

        if(!file_exists("$basePath/$country-icon.png")) {
            throw new Exception('Could not find country flag');
        }

        $flagData = file_get_contents("$basePath/$country-icon.png");
        $flagData = base64_encode($flagData);
        return new ByteArray($flagData);
    }
}