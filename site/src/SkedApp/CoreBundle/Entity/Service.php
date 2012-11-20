<?php
namespace SkedApp\CoreBundle\Entity ;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * SkedApp\CoreBundle\Entity\Service
 *
 * @ORM\Entity(repositoryClass="SkedApp\CoreBundle\Repository\ServiceRepository")
 * @ORM\Table(name="service")
 *
 * @author Ronald Conco <ronald.conco@gmail.com>
 * @package SkedAppCoreBundle
 * @subpackage Entity
 * @version 0.0.1
 */
class Service
{

    /**
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @var integer
     */
    protected $id ;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=50)
     */
    protected $name ;
    
    /**
     * @var string $speciality
     * 
     * @ORM\Column(name="description", type="text", length=500, nullable=true)
     */
    protected $description;    
    
    /**
     * @var boolean
     *
     * @ORM\Column(name="is_deleted", type="boolean")
     */
    protected $isDeleted;    
    
    /**
     * @var SkedApp\CoreBundle\Entity\Category
     * 
     *
     * @ORM\ManyToOne(targetEntity="SkedApp\CoreBundle\Entity\Category", inversedBy="services")
     * @ORM\JoinColumn(name="category_id", referencedColumnName="id")
     */
    protected $category; 
    
    /**
     * @var AppointmentDuration
     *
     * @ORM\ManyToOne(targetEntity="SkedApp\CoreBundle\Entity\AppointmentDuration")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="appointment_duration_id", referencedColumnName="id")
     * })
     */
    protected $appointmentDuration;    
    
    /**
     * @var datetime $createdAt
     *
     * @ORM\Column(name="created_at", type="datetime", nullable=true)
     */
    protected $createdAt;

    /**
     * @var datetime $updatedAt
     *
     * @ORM\Column(name="updated_at", type="datetime", nullable=true)
     */
    protected $updatedAt;    
    
    public function __construct( $name = null )
    {
        $this->name = $name ;
        $this->isDeleted = false;
        $this->createdAt = new \DateTime();
        $this->updatedAt = new \DateTime();
    }

    public function __toString()
    {
        return $this->name ;
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id ;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return Service
     */
    public function setName($name)
    {
        $this->name = $name;
    
        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set isDeleted
     *
     * @param boolean $isDeleted
     * @return Service
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
     * Set category
     *
     * @param \SkedApp\CoreBundle\Entity\Category $category
     * @return Service
     */
    public function setCategory(\SkedApp\CoreBundle\Entity\Category $category = null)
    {
        $this->category = $category;
    
        return $this;
    }

    /**
     * Get category
     *
     * @return \SkedApp\CoreBundle\Entity\Category 
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * Set appointmentDuration
     *
     * @param \SkedApp\CoreBundle\Entity\AppointmentDuration $appointmentDuration
     * @return Service
     */
    public function setAppointmentDuration(\SkedApp\CoreBundle\Entity\AppointmentDuration $appointmentDuration = null)
    {
        $this->appointmentDuration = $appointmentDuration;
    
        return $this;
    }

    /**
     * Get appointmentDuration
     *
     * @return \SkedApp\CoreBundle\Entity\AppointmentDuration 
     */
    public function getAppointmentDuration()
    {
        return $this->appointmentDuration;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return Service
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
     * @return Service
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
     * Set description
     *
     * @param string $description
     * @return Service
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
}