<?php

/**
 * Generated base entity
 * [DO NOT MODIFY]
 */

namespace Conpherence\Entities\Base;

use Conpherence\Entities;
use Conpherence\Entities\Base\BaseEntity;
use \DateTime;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Conpherence\Entities\Session
 *
 * @MappedSuperclass
 * @HasLifecycleCallbacks
 *
 * @Table(name="`Session`")
 */
abstract class BaseSession extends BaseEntity
{

    /**
     * @Id
     * @Column(name="`id`", type="integer", options={"unsigned"=true})
     * @GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @Column(name="`title`", type="string", length=255)
     */
    protected $title;

    /**
     * @Column(name="`description`", type="text", nullable=true)
     */
    protected $description;

    /**
     * @Column(name="`date`", type="datetime")
     */
    protected $date;

    /**
     * @ManyToOne(targetEntity="Conpherence\Entities\Speaker", inversedBy="sessions", cascade={"persist"})
     * @JoinColumn(name="speakerId", referencedColumnName="id", onDelete="CASCADE", nullable=false)
     */
    protected $speaker;

    /**
     * @ManyToMany(targetEntity="Conpherence\Entities\Event", mappedBy="sessions", cascade={"persist"})
     */
    protected $events;

    public function __construct()
    {
        $this->events = new ArrayCollection();
    }

    /**
     * Set the value of id.
     *
     * @param integer $id
     * @return \Conpherence\Entities\Session
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
     * Set the value of title.
     *
     * @param string $title
     * @return \Conpherence\Entities\Session
     */
    public function setTitle($title = null)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get the value of title.
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set the value of description.
     *
     * @param string $description
     * @return \Conpherence\Entities\Session
     */
    public function setDescription($description = null)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get the value of description.
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set the value of date.
     *
     * @param DateTime $date
     * @return \Conpherence\Entities\Session
     */
    public function setDate($date = null)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get the value of date.
     *
     * @return datetime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set Speaker entity (many to one).
     *
     * @param \Conpherence\Entities\Speaker $speaker
     * @return \Conpherence\Entities\Session
     */
    public function setSpeaker(Entities\Speaker $speaker = null)
    {
        $this->speaker = $speaker;

        return $this;
    }

    /**
     * Get Speaker entity (many to one).
     *
     * @return \Conpherence\Entities\Speaker
     */
    public function getSpeaker()
    {
        return $this->speaker;
    }

    /**
     * Add Event entity to collection.
     *
     * @param \Conpherence\Entities\Event $event
     * @return \Conpherence\Entities\Session
     */
    public function addEvent(Entities\Event $event = null)
    {
        $this->events[] = $event;

        return $this;
    }

    /**
     * Get Event entity collection.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getEvents()
    {
        return $this->events;
    }

    public function __sleep()
    {
        return array('id', 'title', 'description', 'date', 'speakerId');
    }
}