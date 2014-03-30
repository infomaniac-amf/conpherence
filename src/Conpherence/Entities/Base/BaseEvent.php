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
 * Conpherence\Entities\Event
 *
 * @MappedSuperclass
 * @HasLifecycleCallbacks
 *
 * @Table(name="`Event`")
 */
abstract class BaseEvent extends BaseEntity
{

    /**
     * @Id
     * @Column(name="`id`", type="integer", options={"unsigned"=true})
     * @GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @Column(name="`name`", type="string", length=255)
     */
    protected $name;

    /**
     * @Column(name="`description`", type="text", nullable=true)
     */
    protected $description;

    /**
     * @Column(name="`hashtag`", type="string", length=255, nullable=true)
     */
    protected $hashtag;

    /**
     * @Column(name="`url`", type="text", nullable=true)
     */
    protected $url;

    /**
     * @ManyToMany(targetEntity="Conpherence\Entities\Session", inversedBy="events", cascade={"persist"}, fetch="EAGER")
     * @JoinTable(name="EventSession",
     *     joinColumns={@JoinColumn(name="eventId", referencedColumnName="id")},
     *     inverseJoinColumns={@JoinColumn(name="sessionId", referencedColumnName="id")}
     * )
     */
    protected $sessions;

    public function __construct()
    {
        $this->sessions = new ArrayCollection();
    }

    /**
     * Set the value of id.
     *
     * @param integer $id
     * @return \Conpherence\Entities\Event
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
     * Set the value of name.
     *
     * @param string $name
     * @return \Conpherence\Entities\Event
     */
    public function setName($name = null)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get the value of name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set the value of description.
     *
     * @param string $description
     * @return \Conpherence\Entities\Event
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
     * Set the value of hashtag.
     *
     * @param string $hashtag
     * @return \Conpherence\Entities\Event
     */
    public function setHashtag($hashtag = null)
    {
        $this->hashtag = $hashtag;

        return $this;
    }

    /**
     * Get the value of hashtag.
     *
     * @return string
     */
    public function getHashtag()
    {
        return $this->hashtag;
    }

    /**
     * Set the value of url.
     *
     * @param string $url
     * @return \Conpherence\Entities\Event
     */
    public function setUrl($url = null)
    {
        $this->url = $url;

        return $this;
    }

    /**
     * Get the value of url.
     *
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Add Session entity to collection.
     *
     * @param \Conpherence\Entities\Session $session
     * @return \Conpherence\Entities\Event
     */
    public function addSession(Entities\Session $session = null)
    {
        $session->addEvent($this);
        $this->sessions[] = $session;

        return $this;
    }

    /**
     * Get Session entity collection.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getSessions()
    {
        return $this->sessions;
    }

    /**
     * Remove Session entity from collection.
     *
     * @param \Conpherence\Entities\Session $session
     * @param bool $delete
     * @return \Conpherence\Entities\Event
     */

    public function removeSession(Entities\Session $session = null, $delete=false)
    {
        if($session) {
            $this->sessions->removeElement($session);
            if ($delete) {
                $this->getEntityManager()->remove($session);
            }
        }

        return $this;
    }

    public function __sleep()
    {
        return array('id', 'name', 'description', 'hashtag', 'url');
    }
}