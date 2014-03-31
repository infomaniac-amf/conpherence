<?php

namespace Conpherence\Entities;

use Conpherence\Entities\Base\BaseSpeaker;
use Exception;
use Illuminate\Support\Facades\Config;
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
            'id'       => $this->getId(),
            'image'    => $this->getImage(),
            'name'     => $this->getName(),
            'country'  => $this->getCountry(),
            'twitter'  => $this->getTwitterHandle(),
            'bio'      => $this->getBio(),
            'sessions' => $this->getSessions()->toArray(),
            'flag'     => $this->getFlag()
        );
    }

    public function getImage()
    {
        $image = parent::getImage();
        if (empty($image)) {
            return null;
        }

        $data = stream_get_contents($image->getData());
        if(empty($data)) {
            return null;
        }

        return new ByteArray(base64_encode($data));
    }

    private function getFlag()
    {
        $basePath = Config::get('app.flags');
        $country  = str_replace(' ', '-', $this->getCountry());

        if (!file_exists("$basePath/$country-icon.png")) {
            throw new Exception('Could not find country flag');
        }

        $flagData = file_get_contents("$basePath/$country-icon.png");
        $flagData = base64_encode($flagData);

        return new ByteArray($flagData);
    }
}