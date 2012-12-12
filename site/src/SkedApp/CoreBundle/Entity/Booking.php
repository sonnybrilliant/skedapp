<?php

namespace SkedApp\CoreBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints as DoctrineAssert;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * SkedApp\CoreBundle\Entity\Booking
 *
 * @ORM\Entity(repositoryClass="SkedApp\CoreBundle\Repository\BookingRepository")
 * @ORM\Table(name="booking")
 * @ORM\HasLifecycleCallbacks
 *
 * @author Ronald Conco <ronald.conco@gmail.com>
 * @package SkedAppCoreBundle
 * @subpackage Entity
 * @version 0.0.1
 */
class Booking
{

    /**
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @var integer
     */
    protected $id;

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
     * @var Customer
     *
     * @ORM\ManyToOne(targetEntity="SkedApp\CoreBundle\Entity\Customer")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="customer_id", referencedColumnName="id")
     * })
     */
    protected $customer;    

    /**
     * @var Service
     *
     * @ORM\ManyToOne(targetEntity="SkedApp\CoreBundle\Entity\Service")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="service_id", referencedColumnName="id")
     * })
     */
    protected $service;

    /**
     * @var string $description
     *
     * @Assert\MaxLength(limit= 500, message="Booking description has a limit of {{ limit }} characters.")
     *
     * @ORM\Column(name="description", type="text", length=500, nullable=true)
     */
    protected $description;

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
     * @var boolean
     *
     * @ORM\Column(name="is_deleted", type="boolean")
     */
    protected $isDeleted;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_active", type="boolean")
     */
    protected $isActive;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_cancelled", type="boolean")
     */
    protected $isCancelled;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_leave", type="boolean")
     */
    protected $isLeave;
    
    /**
     * @var boolean
     *
     * @ORM\Column(name="is_reminder_sent", type="boolean")
     */
    protected $isReminderSent;    

    /**
     * @var datetime
     *
     * @ORM\Column(name="appointment_date", type="date")
     */
    protected $appointmentDate;

    /**
     * @var datetime
     *
     * @ORM\Column(name="hidden_appointment_start_time", type="datetime", nullable=true)
     */
    protected $hiddenAppointmentStartTime;

    /**
     * @var datetime
     *
     * @ORM\Column(name="hidden_appointment_end_time", type="datetime" , nullable=true)
     */
    protected $hiddenAppointmentEndTime;

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
        $this->isDeleted = false;
        $this->isLeave = false;
        $this->isActive = true;
        $this->isCancelled = false;
        $this->isReminderSent = false;
        $this->createdAt = new \DateTime();
        $this->updatedAt = new \DateTime();
    }

    /**
     * Create appointment date with time
     *
     * @ORM\PreUpdate()
     * @ORM\PrePersist()
     */
    public function createAppointmentStartTime()
    {
        $dateTime = strtotime("+" . $this->getStartTimeslot()->getWeight() * 900 . " seconds", $this->getAppointmentDate()->format('U'));
        $currentDateTime = new \DateTime();
        $currentDateTime->setTimestamp($dateTime);
        $this->setHiddenAppointmentStartTime($currentDateTime);
        return;
    }

    /**
     * Create appointment date with time
     *
     * @ORM\PreUpdate()
     * @ORM\PrePersist()
     */
    public function createAppointmentEndTime()
    {
        $dateTime = strtotime("+" . $this->getEndTimeslot()->getWeight() * 900 . " seconds", $this->getAppointmentDate()->format('U'));
        $currentDateTime = new \DateTime();
        $currentDateTime->setTimestamp($dateTime);
        $this->setHiddenAppointmentEndTime($currentDateTime);
        return;
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
     * Set description
     *
     * @param string $description
     * @return Booking
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set isDeleted
     *
     * @param boolean $isDeleted
     * @return Booking
     */
    public function setIsDeleted($isDeleted)
    {
        $this->isDeleted = $isDeleted;

        return $this;
    }

    /**
     * Get isDeleted
     *
     * @return boolean
     */
    public function getIsDeleted()
    {
        return $this->isDeleted;
    }

    /**
     * Set isActive
     *
     * @param boolean $isActive
     * @return Booking
     */
    public function setIsActive($isActive)
    {
        $this->isActive = $isActive;

        return $this;
    }

    /**
     * Get isActive
     *
     * @return boolean
     */
    public function getIsActive()
    {
        return $this->isActive;
    }

    /**
     * Set isCancelled
     *
     * @param boolean $isCancelled
     * @return Booking
     */
    public function setIsCancelled($isCancelled)
    {
        $this->isCancelled = $isCancelled;

        return $this;
    }

    /**
     * Get isCancelled
     *
     * @return boolean
     */
    public function getIsCancelled()
    {
        return $this->isCancelled;
    }

    /**
     * Set isLeave
     *
     * @param boolean $isLeave
     * @return Booking
     */
    public function setIsLeave($isLeave)
    {
        $this->isLeave = $isLeave;

        return $this;
    }

    /**
     * Get isLeave
     *
     * @return boolean
     */
    public function getIsLeave()
    {
        return $this->isLeave;
    }

    /**
     * Set appointmentDate
     *
     * @param \DateTime $appointmentDate
     * @return Booking
     */
    public function setAppointmentDate($appointmentDate)
    {
        $this->appointmentDate = $appointmentDate;

        return $this;
    }

    /**
     * Get appointmentDate
     *
     * @return \DateTime
     */
    public function getAppointmentDate()
    {
        return $this->appointmentDate;
    }

    /**
     * Set hiddenAppointmentStartTime
     *
     * @param \DateTime $hiddenAppointmentStartTime
     * @return Booking
     */
    public function setHiddenAppointmentStartTime($hiddenAppointmentStartTime)
    {
        $this->hiddenAppointmentStartTime = $hiddenAppointmentStartTime;

        return $this;
    }

    /**
     * Get hiddenAppointmentStartTime
     *
     * @return \DateTime
     */
    public function getHiddenAppointmentStartTime()
    {
        return $this->hiddenAppointmentStartTime;
    }

    /**
     * Set hiddenAppointmentEndTime
     *
     * @param \DateTime $hiddenAppointmentEndTime
     * @return Booking
     */
    public function setHiddenAppointmentEndTime($hiddenAppointmentEndTime)
    {
        $this->hiddenAppointmentEndTime = $hiddenAppointmentEndTime;

        return $this;
    }

    /**
     * Get hiddenAppointmentEndTime
     *
     * @return \DateTime
     */
    public function getHiddenAppointmentEndTime()
    {
        return $this->hiddenAppointmentEndTime;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return Booking
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
     * @return Booking
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
     * Set consultant
     *
     * @param \SkedApp\CoreBundle\Entity\Consultant $consultant
     * @return Booking
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
     * Set service
     *
     * @param \SkedApp\CoreBundle\Entity\Service $service
     * @return Booking
     */
    public function setService(\SkedApp\CoreBundle\Entity\Service $service = null)
    {
        $this->service = $service;

        return $this;
    }

    /**
     * Get service
     *
     * @return \SkedApp\CoreBundle\Entity\Service
     */
    public function getService()
    {
        return $this->service;
    }

    /**
     * Set startTimeslot
     *
     * @param \SkedApp\CoreBundle\Entity\Timeslots $startTimeslot
     * @return Booking
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
     * @return Booking
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
     * Set isReminderSent
     *
     * @param boolean $isReminderSent
     * @return Booking
     */
    public function setIsReminderSent($isReminderSent)
    {
        $this->isReminderSent = $isReminderSent;
    
        return $this;
    }

    /**
     * Get isReminderSent
     *
     * @return boolean 
     */
    public function getIsReminderSent()
    {
        return $this->isReminderSent;
    }

    /**
     * Set customer
     *
     * @param \SkedApp\CoreBundle\Entity\Customer $customer
     * @return Booking
     */
    public function setCustomer(\SkedApp\CoreBundle\Entity\Customer $customer = null)
    {
        $this->customer = $customer;
    
        return $this;
    }

    /**
     * Get customer
     *
     * @return \SkedApp\CoreBundle\Entity\Customer 
     */
    public function getCustomer()
    {
        return $this->customer;
    }
}