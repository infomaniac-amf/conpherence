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
 * Conpherence\Entities\Speaker
 *
 * @MappedSuperclass
 * @HasLifecycleCallbacks
 *
 * @Table(name="`Speaker`")
 */
abstract class BaseSpeaker extends BaseEntity
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
     * @Column(name="`country`", type="string", length=255)
     */
    protected $country;

    /**
     * @Column(name="`twitterHandle`", type="string", length=255, nullable=true)
     */
    protected $twitterHandle;

    /**
     * @OneToOne(targetEntity="Conpherence\Entities\Bio", mappedBy="speaker", cascade={"persist"})
     * @JoinColumn(name="id", nullable=false)
     */
    protected $bio;

    /**
     * @OneToMany(targetEntity="Conpherence\Entities\Session", mappedBy="speaker", cascade={"persist"})
     * @JoinColumn(name="id", nullable=false)
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
     * @return \Conpherence\Entities\Speaker
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
     * @return \Conpherence\Entities\Speaker
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
     * Set the value of country.
     *
     * @param string $country
     * @return \Conpherence\Entities\Speaker
     */
    public function setCountry($country = null)
    {
        $this->country = $country;

        return $this;
    }

    /**
     * Get the value of country.
     *
     * @return string
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * Set the value of twitterHandle.
     *
     * @param string $twitterHandle
     * @return \Conpherence\Entities\Speaker
     */
    public function setTwitterHandle($twitterHandle = null)
    {
        $this->twitterHandle = $twitterHandle;

        return $this;
    }

    /**
     * Get the value of twitterHandle.
     *
     * @return string
     */
    public function getTwitterHandle()
    {
        return $this->twitterHandle;
    }

    /**
     * Set Bio entity (one to one).
     *
     * @param \Conpherence\Entities\Bio $bio
     * @return \Conpherence\Entities\Speaker
     */
    public function setBio(Entities\Bio $bio = null)
    {
        $this->bio = $bio;

        return $this;
    }

    /**
     * Get Bio entity (one to one).
     *
     * @return \Conpherence\Entities\Bio
     */
    public function getBio()
    {
        return $this->bio;
    }

    /**
     * Add Session entity to collection (one to many).
     *
     * @param \Conpherence\Entities\Session $session
     * @return \Conpherence\Entities\Speaker
     */
    public function addSession(Entities\Session $session = null)
    {
        if($session) {
            $session->setSpeaker($this);
        }

        $this->sessions[] = $session;

        return $this;
    }

    /**
     * Get Session entity collection (one to many).
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getSessions()
    {
        return $this->sessions;
    }

    /**
     * Remove Session entity from collection (one to many).
     *
     * @param \Conpherence\Entities\Session $session
     * @param bool $delete
     * @return \Conpherence\Entities\Speaker
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
        return array('id', 'name', 'country', 'twitterHandle');
    }
}