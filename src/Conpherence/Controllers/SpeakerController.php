<?php
namespace Conpherence\Controllers;

use Conpherence\Entities\Speaker;

class SpeakerController extends AMFController
{
    public function getIndex()
    {
        return $this->createAMFResponse(Speaker::getRepository()->findAll());
    }
} 