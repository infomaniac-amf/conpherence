<?php
namespace Conpherence\Controllers;

use Conpherence\Entities\Event;
use Conpherence\Entities\Session;
use Conpherence\Entities\Speaker;
use Exception;
use Illuminate\Support\Facades\Request;
use Infomaniac\AMF\AMF;
use Symfony\Component\HttpFoundation\Response;

class SpeakerController extends AMFController
{
    public function getIndex()
    {
        return $this->createAMFResponse(Speaker::getRepository()->findAll());
    }

    public function postIndex()
    {
        $request = Request::instance();

        /**
         * @var $speaker Speaker
         */
        $speaker = AMF::deserialize($request->getContent());

        /**
         * @var $event Event
         */
        $event = Event::getRepository()->find(1);

        if (count($speaker->getSessions())) {
            foreach ($speaker->getSessions() as $session) {
                /**
                 * @var $session Session
                 */
                $event->addSession($session);
            }
        }

        /**
         * @var $speaker Speaker
         */

        $entityManager = $speaker->getEntityManager();
        $entityManager->persist($event);
        $entityManager->flush($event);

        return $this->createAMFResponse(array('success' => true));
    }
} 