<?php
namespace SkedApp\CoreBundle\Entity ;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * SkedApp\CoreBundle\Entity\AppointmentDuration
 *
 * @ORM\Entity(repositoryClass="SkedApp\CoreBundle\Repository\AppointmentDurationRepository")
 * @ORM\Table(name="appointment_duration")
 *
 * @author Ronald Conco <ronald.conco@gmail.com>
 * @package SkedAppCoreBundle
 * @subpackage Entity
 * @version 0.0.1
 */
class AppointmentDuration
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
     * @ORM\Column(name="name", type="string", length=20)
     */
    protected $name ;
    
    /**
     * @var string
     *
     * @ORM\Column(name="duration", type="integer", length=3)
     */
    protected $duration ;    
    
    public function __construct( $name )
    {
        $this->name = $name ;
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
     * @return AppointmentDuration
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
     * Set duration
     *
     * @param integer $duration
     * @return AppointmentDuration
     */
    public function setDuration($duration)
    {
        $this->duration = $duration;
    
        return $this;
    }

    /**
     * Get duration
     *
     * @return integer 
     */
    public function getDuration()
    {
        return $this->duration;
    }
}