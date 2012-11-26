<?php

namespace SkedApp\CoreBundle\Entity ;

use Doctrine\ORM\Mapping as ORM;

/**
 * SkedApp\CoreBundle\Entity\Timeslots
 *
 * @ORM\Entity(repositoryClass="SkedApp\CoreBundle\Repository\TitleRepository")
 * @ORM\Table(name="timeslots")
 *
 * @author Ronald Conco <ronald.conco@gmail.com>
 * @package SkedAppCoreBundle
 * @subpackage Entity
 * @version 0.0.1
 */
class Timeslots
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
     * @ORM\Column(name="slot", type="string", length=50)
     */
    protected $slot ;

    public function __construct( $slot )
    {
        $this->slot = $slot ;
    }

    public function __toString()
    {
        return $this->slot ;
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
     * Set slot
     *
     * @param string $slot
     * @return Timeslots
     */
    public function setSlot($slot)
    {
        $this->slot = $slot;
    
        return $this;
    }

    /**
     * Get slot
     *
     * @return string 
     */
    public function getSlot()
    {
        return $this->slot;
    }
}