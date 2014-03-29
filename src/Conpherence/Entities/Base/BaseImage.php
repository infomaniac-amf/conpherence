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
 * Conpherence\Entities\Image
 *
 * @MappedSuperclass
 * @HasLifecycleCallbacks
 *
 * @Table(name="`Image`")
 */
abstract class BaseImage extends BaseEntity
{

    /**
     * @Id
     * @Column(name="`id`", type="integer", options={"unsigned"=true})
     * @GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @Column(name="`data`", type="blob")
     */
    protected $data;

    /**
     * @OneToMany(targetEntity="Conpherence\Entities\Speaker", mappedBy="image", cascade={"persist"})
     * @JoinColumn(name="id")
     */
    protected $speakers;

    public function __construct()
    {
        $this->speakers = new ArrayCollection();
    }

    /**
     * Set the value of id.
     *
     * @param integer $id
     * @return \Conpherence\Entities\Image
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
     * Set the value of data.
     *
     * @param string $data
     * @return \Conpherence\Entities\Image
     */
    public function setData($data = null)
    {
        $this->data = $data;

        return $this;
    }

    /**
     * Get the value of data.
     *
     * @return string
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * Add Speaker entity to collection (one to many).
     *
     * @param \Conpherence\Entities\Speaker $speaker
     * @return \Conpherence\Entities\Image
     */
    public function addSpeaker(Entities\Speaker $speaker = null)
    {
        if($speaker) {
            $speaker->setImage($this);
        }

        $this->speakers[] = $speaker;

        return $this;
    }

    /**
     * Get Speaker entity collection (one to many).
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getSpeakers()
    {
        return $this->speakers;
    }

    /**
     * Remove Speaker entity from collection (one to many).
     *
     * @param \Conpherence\Entities\Speaker $speaker
     * @param bool $delete
     * @return \Conpherence\Entities\Image
     */
    public function removeSpeaker(Entities\Speaker $speaker = null, $delete=false)
    {
        if($speaker) {
            $this->speakers->removeElement($speaker);
            if ($delete) {
                $this->getEntityManager()->remove($speaker);
            }
        }

        return $this;
    }

    public function __sleep()
    {
        return array('id', 'data');
    }
}