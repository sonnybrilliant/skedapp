<?php

namespace SkedApp\CoreBundle\Entity ;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
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
     * @var SkedApp\CoreBundle\Entity\Category
     * 
     *
     * @ORM\ManyToOne(targetEntity="SkedApp\CoreBundle\Entity\Category", inversedBy="services")
     * @ORM\JoinColumn(name="category_id", referencedColumnName="id")
     */
    protected $category; 
    

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
     * Set category
     *
     * @param SkedApp\CoreBundle\Entity\Category $category
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
     * @return SkedApp\CoreBundle\Entity\Category 
     */
    public function getCategory()
    {
        return $this->category;
    }
}