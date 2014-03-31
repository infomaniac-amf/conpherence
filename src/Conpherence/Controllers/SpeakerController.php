<?php
namespace Conpherence\Controllers;

use Conpherence\Entities\Speaker;
use Illuminate\Support\Facades\Request;
use Infomaniac\AMF\AMF;

class SpeakerController extends AMFController
{
    public function getIndex()
    {
        return $this->createAMFResponse(Speaker::getRepository()->findAll());
    }

    public function postIndex()
    {
        $request = Request::instance();
        $speaker = AMF::deserialize($request->getContent());

        /**
         * @var $speaker Speaker
         */

        $entityManager = $speaker->getEntityManager();
        $entityManager->persist($speaker);
        $entityManager->flush($speaker);

        return $this->createAMFResponse($speaker);
    }
} 