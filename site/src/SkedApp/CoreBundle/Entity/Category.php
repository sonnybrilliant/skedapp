<?php

namespace SkedApp\CoreBundle\Entity ;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * SkedApp\CoreBundle\Entity\Category
 *
 * @ORM\Entity(repositoryClass="SkedApp\CoreBundle\Repository\CategoryRepository")
 * @ORM\Table(name="category")
 *
 * @author Ronald Conco <ronald.conco@gmail.com>
 * @package SkedAppCoreBundle
 * @subpackage Entity
 * @version 0.0.1
 */
class Category
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
     * @ORM\OneToMany(targetEntity="SkedApp\CoreBundle\Entity\Service", mappedBy="category")
     */
    protected $services;     

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
     */
    public function setName( $name )
    {
        $this->name = $name ;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name ;
    }


    /**
     * Add services
     *
     * @param SkedApp\CoreBundle\Entity\Service $services
     * @return Category
     */
    public function addService(\SkedApp\CoreBundle\Entity\Service $services)
    {
        $this->services[] = $services;
    
        return $this;
    }

    /**
     * Remove services
     *
     * @param SkedApp\CoreBundle\Entity\Service $services
     */
    public function removeService(\SkedApp\CoreBundle\Entity\Service $services)
    {
        $this->services->removeElement($services);
    }

    /**
     * Get services
     *
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getServices()
    {
        return $this->services;
    }
}