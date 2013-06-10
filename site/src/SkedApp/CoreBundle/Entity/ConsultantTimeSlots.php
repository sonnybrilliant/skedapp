<?php

namespace SkedApp\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints as DoctrineAssert;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * SkedApp\CoreBundle\Entity\ConsultantTimeSlots
 *
 * @ORM\Table(name="consultant_time_slots")
 * @ORM\Entity(repositoryClass="SkedApp\CoreBundle\Repository\ConsultantTimeSlotsRepository")
 *
 * @author Ronald Conco <ronald.conco@gmail.com>
 * @package SkedAppCoreBundle
 * @subpackage Entity
 * @version 0.0.1
 */
class ConsultantTimeSlots
{

    /**
     * @var integer $id
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * @var daysOfTheWeek
     *
     * @ORM\ManyToOne(targetEntity="SkedApp\CoreBundle\Entity\DaysOfTheWeek")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="days_of_the_week_id", referencedColumnName="id")
     * })
     */
    protected $daysOfTheWeek;

    /**
     * @var Consultant
     *
     * @ORM\ManyToOne(targetEntity="SkedApp\CoreBundle\Entity\Consultant")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="consultant_id", referencedColumnName="id")
     * })
     */
    protected $consultant;
    
    /**
     *
     * @ORM\OneToMany(targetEntity="SkedApp\CoreBundle\Entity\Slots", cascade={"all"} , mappedBy="consultantTimeSlot")
     */
    public $slots;

    /**
     * @var datetime
     *
     * @ORM\Column(name="created_at", type="datetime")
     */
    protected $createdAt;

    /**
     * @var datetime
     *
     * @ORM\Column(name="updated_at", type="datetime")
     */
    protected $updatedAt;
    
    /**
     * @var string
     */
    protected $tokenDeletedSlot;

    public function __construct()
    {
        $this->slots = new ArrayCollection();
        $this->createdAt = new \DateTime();
        $this->updatedAt = new \DateTime();
    }

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return ConsultantTimeSlots
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime 
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set updatedAt
     *
     * @param \DateTime $updatedAt
     * @return ConsultantTimeSlots
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * Get updatedAt
     *
     * @return \DateTime 
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * Set daysOfTheWeek
     *
     * @param \SkedApp\CoreBundle\Entity\DaysOfTheWeek $daysOfTheWeek
     * @return ConsultantTimeSlots
     */
    public function setDaysOfTheWeek(\SkedApp\CoreBundle\Entity\DaysOfTheWeek $daysOfTheWeek = null)
    {
        $this->daysOfTheWeek = $daysOfTheWeek;

        return $this;
    }

    /**
     * Get daysOfTheWeek
     *
     * @return \SkedApp\CoreBundle\Entity\DaysOfTheWeek 
     */
    public function getDaysOfTheWeek()
    {
        return $this->daysOfTheWeek;
    }

    /**
     * Set consultant
     *
     * @param \SkedApp\CoreBundle\Entity\Consultant $consultant
     * @return ConsultantTimeSlots
     */
    public function setConsultant(\SkedApp\CoreBundle\Entity\Consultant $consultant = null)
    {
        $this->consultant = $consultant;

        return $this;
    }

    /**
     * Get consultant
     *
     * @return \SkedApp\CoreBundle\Entity\Consultant 
     */
    public function getConsultant()
    {
        return $this->consultant;
    }



    /**
     * Add slots
     *
     * @param \SkedApp\CoreBundle\Entity\Slots $slots
     * @return ConsultantTimeSlots
     */
    public function addSlot(\SkedApp\CoreBundle\Entity\Slots $slots)
    {
        $this->slots[] = $slots;
    
        return $this;
    }

    /**
     * Remove slots
     *
     * @param \SkedApp\CoreBundle\Entity\Slots $slots
     */
    public function removeSlot(\SkedApp\CoreBundle\Entity\Slots $slots)
    {
        $this->slots->removeElement($slots);
    }

    /**
     * Get slots
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getSlots()
    {
        return $this->slots;
    }
    
    public function getTokenDeletedSlot()
    {
        return $this->tokenDeletedSlot;
    }
    
    public function setTokenDeletedSlot($token)
    {
        return $this->tokenDeletedSlot = $token;
    }
}