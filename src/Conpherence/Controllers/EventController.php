<?php
namespace Conpherence\Controllers;

use Conpherence\Entities\Event;

class EventController extends AMFController
{
    public function getIndex()
    {
        return $this->createAMFResponse(Event::getRepository()->findAll());
    }
} 