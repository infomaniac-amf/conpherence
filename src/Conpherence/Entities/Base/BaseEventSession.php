<?php

/**
 * Generated base entity
 * [DO NOT MODIFY]
 */

namespace Conpherence\Entities\Base;

use Conpherence\Entities;
use Conpherence\Entities\Base\BaseEntity;
use \DateTime;

/**
 * Conpherence\Entities\EventSession
 *
 * @MappedSuperclass
 * @HasLifecycleCallbacks
 *
 * @Table(name="`EventSession`")
 */
abstract class BaseEventSession extends BaseEntity
{

    /**
     * @Id
     * @Column(name="`id`", type="integer", options={"unsigned"=true})
     * @GeneratedValue(strategy="AUTO")
     */
    protected $id;

    public function __construct()
    {
    }

    /**
     * Set the value of id.
     *
     * @param integer $id
     * @return \Conpherence\Entities\EventSession
     */
    public function setId($id = null)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Get the value of id.
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set Event entity (many to one).
     *
     * @param \Conpherence\Entities\Event $event
     * @return \Conpherence\Entities\EventSession
     */
    public function setEvent(Entities\Event $event = null)
    {
        $this->event = $event;

        return $this;
    }

    /**
     * Get Event entity (many to one).
     *
     * @return \Conpherence\Entities\Event
     */
    public function getEvent()
    {
        return $this->event;
    }

    /**
     * Set Session entity (many to one).
     *
     * @param \Conpherence\Entities\Session $session
     * @return \Conpherence\Entities\EventSession
     */
    public function setSession(Entities\Session $session = null)
    {
        $this->session = $session;

        return $this;
    }

    /**
     * Get Session entity (many to one).
     *
     * @return \Conpherence\Entities\Session
     */
    public function getSession()
    {
        return $this->session;
    }

    public function __sleep()
    {
        return array('id', 'eventId', 'sessionId');
    }
}