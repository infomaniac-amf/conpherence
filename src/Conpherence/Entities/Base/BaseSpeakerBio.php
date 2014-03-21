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
 * Conpherence\Entities\SpeakerBio
 *
 * @MappedSuperclass
 * @HasLifecycleCallbacks
 *
 * @Table(name="`SpeakerBio`")
 */
abstract class BaseSpeakerBio extends BaseEntity
{

    /**
     * @Id
     * @Column(name="`id`", type="integer")
     * @GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @Column(name="`text`", type="text", nullable=true)
     */
    protected $text;

    /**
     * @OneToOne(targetEntity="Conpherence\Entities\Speaker", inversedBy="speakerBio", cascade={"persist"})
     * @JoinColumn(name="speakerId", referencedColumnName="id", nullable=false)
     */
    protected $speaker;

    public function __construct()
    {
    }

    /**
     * Set the value of id.
     *
     * @param integer $id
     * @return \Conpherence\Entities\SpeakerBio
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
     * Set the value of text.
     *
     * @param string $text
     * @return \Conpherence\Entities\SpeakerBio
     */
    public function setText($text = null)
    {
        $this->text = $text;

        return $this;
    }

    /**
     * Get the value of text.
     *
     * @return string
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * Set Speaker entity (one to one).
     *
     * @param \Conpherence\Entities\Speaker $speaker
     * @return \Conpherence\Entities\SpeakerBio
     */
    public function setSpeaker(Entities\Speaker $speaker = null)
    {
        if($speaker) {
            $speaker->setSpeakerBio($this);
        }

        $this->speaker = $speaker;

        return $this;
    }

    /**
     * Get Speaker entity (one to one).
     *
     * @return \Conpherence\Entities\Speaker
     */
    public function getSpeaker()
    {
        return $this->speaker;
    }

    public function __sleep()
    {
        return array('id', 'text', 'speakerId');
    }
}