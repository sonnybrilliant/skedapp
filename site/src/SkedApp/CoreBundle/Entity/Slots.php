<?php
namespace SkedApp\CoreBundle\Entity ;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints as DoctrineAssert;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * SkedApp\CoreBundle\Entity\Slots
 *
 * @ORM\Table(name="slots")
 * @ORM\Entity(repositoryClass="SkedApp\CoreBundle\Repository\SlotsRepository")
 *
 * @author Ronald Conco <ronald.conco@gmail.com>
 * @package SkedAppCoreBundle
 * @subpackage Entity
 * @version 0.0.1
 */
class Slots
{

    /**
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @var integer
     */
    protected $id;    
    
    /**
     * @var Timeslot
     *
     * @ORM\ManyToOne(targetEntity="SkedApp\CoreBundle\Entity\Timeslots")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="start_time_slot_id", referencedColumnName="id")
     * })
     */
    protected $startTimeslot;

    /**
     * @var Timeslot
     *
     * @ORM\ManyToOne(targetEntity="SkedApp\CoreBundle\Entity\Timeslots")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="end_time_slot_id", referencedColumnName="id")
     * })
     */
    protected $endTimeslot; 
    
    /**
     * @var ConsultantTimeslot
     *
     * @ORM\ManyToOne(targetEntity="SkedApp\CoreBundle\Entity\ConsultantTimeSlots" , inversedBy="slots")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="consultant_timeslots_id", referencedColumnName="id")
     * })
     */
    protected $consultantTimeSlot;    
    
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
    
    public function __construct()
    {
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
     * @return Slots
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
     * @return Slots
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
     * Set startTimeslot
     *
     * @param \SkedApp\CoreBundle\Entity\Timeslots $startTimeslot
     * @return Slots
     */
    public function setStartTimeslot(\SkedApp\CoreBundle\Entity\Timeslots $startTimeslot = null)
    {
        $this->startTimeslot = $startTimeslot;
    
        return $this;
    }

    /**
     * Get startTimeslot
     *
     * @return \SkedApp\CoreBundle\Entity\Timeslots 
     */
    public function getStartTimeslot()
    {
        return $this->startTimeslot;
    }

    /**
     * Set endTimeslot
     *
     * @param \SkedApp\CoreBundle\Entity\Timeslots $endTimeslot
     * @return Slots
     */
    public function setEndTimeslot(\SkedApp\CoreBundle\Entity\Timeslots $endTimeslot = null)
    {
        $this->endTimeslot = $endTimeslot;
    
        return $this;
    }

    /**
     * Get endTimeslot
     *
     * @return \SkedApp\CoreBundle\Entity\Timeslots 
     */
    public function getEndTimeslot()
    {
        return $this->endTimeslot;
    }

    /**
     * Set consultantTimeSlot
     *
     * @param \SkedApp\CoreBundle\Entity\ConsultantTimeSlots $consultantTimeSlot
     * @return Slots
     */
    public function setConsultantTimeSlot(\SkedApp\CoreBundle\Entity\ConsultantTimeSlots $consultantTimeSlot)
    {
        $this->consultantTimeSlot = $consultantTimeSlot;
    
        return $this;
    }

    /**
     * Get consultantTimeSlot
     *
     * @return \SkedApp\CoreBundle\Entity\ConsultantTimeSlots 
     */
    public function getConsultantTimeSlot()
    {
        return $this->consultantTimeSlot;
    }
}