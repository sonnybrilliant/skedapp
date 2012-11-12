<?php
namespace SkedApp\CoreBundle\Entity ;

use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Mapping\ClassMetadata;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints as DoctrineAssert;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * SkedApp\CoreBundle\Entity\Company
 *
 * @ORM\Table(name="company")
 * @ORM\Entity(repositoryClass="SkedApp\CoreBundle\Repository\CompanyRepository")
 *
 * @DoctrineAssert\UniqueEntity(fields={"name"}, message="Company name must be unique, please choose another name.")
 */
class Company
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
     * @var string $name
     * 
     * @Assert\NotBlank(message = "Company name cannot be blank!")
     * @Assert\MinLength(limit= 2, message="Company name must have at least {{ limit }} characters.")
     * @Assert\MaxLength(limit= 100, message="Company name has a limit of {{ limit }} characters.")
     *
     * @ORM\Column(name="name", type="string", length=100, nullable=false)
     */
    protected $name;

    /**
     * @var string $accountNumber
     * 
     * @ORM\Column(name="description", type="string", length=254, nullable=false)
     */
    protected $description;
    
    /**
     * @ORM\OneToMany(targetEntity="SkedApp\CoreBundle\Entity\Member", mappedBy="company")
     */
    protected $members;    
    
    /**
     * @ORM\OneToMany(targetEntity="SkedApp\CoreBundle\Entity\Consultant", mappedBy="company")
     */
    protected $consultants;        

    /**
     * @var boolean $isActive
     *
     * @ORM\Column(name="is_active", type="boolean", nullable=false)
     */
    protected $isActive;

    /**
     * @var boolean $isLocked
     *
     * @ORM\Column(name="is_locked", type="boolean", nullable=false)
     */
    protected $isLocked;

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

    public function __construct()
    {
        $this->members    = new ArrayCollection(); 
        $this->setIsLocked(false);
        $this->setIsActive(true);
    }

    public function __toString()
    {
        return $this->getName();
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
     * Set name
     *
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
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
     * Set isActive
     *
     * @param boolean $isActive
     */
    public function setIsActive($isActive)
    {
        $this->isActive = $isActive;
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
     * Set isLocked
     *
     * @param boolean $isLocked
     */
    public function setIsLocked($isLocked)
    {
        $this->isLocked = $isLocked;
    }

    /**
     * Get isLocked
     *
     * @return boolean
     */
    public function getIsLocked()
    {
        return $this->isLocked;
    }

    /**
     * Set createdAt
     *
     * @param datetime $createdAt
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
    }

    /**
     * Get createdAt
     *
     * @return datetime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set updatedAt
     *
     * @param datetime $updatedAt
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;
    }

    /**
     * Get updatedAt
     *
     * @return datetime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * Add members
     *
     * @param SkedApp\CoreBundle\Entity\Member $members
     */
    public function addMember(\SkedApp\CoreBundle\Entity\Member $members)
    {
        $this->members[] = $members;
    }

    /**
     * Get members
     *
     * @return Doctrine\Common\Collections\Collection
     */
    public function getMembers()
    {
        return $this->members;
    }


    /**
     * Remove members
     *
     * @param SkedApp\CoreBundle\Entity\Member $members
     */
    public function removeMember(\SkedApp\CoreBundle\Entity\Member $members)
    {
        $this->members->removeElement($members);
    }

    /**
     * Set description
     *
     * @param string $description
     * @return Company
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
     * Add consultants
     *
     * @param \SkedApp\CoreBundle\Entity\Consultant $consultants
     * @return Company
     */
    public function addConsultant(\SkedApp\CoreBundle\Entity\Consultant $consultants)
    {
        $this->consultants[] = $consultants;
    
        return $this;
    }

    /**
     * Remove consultants
     *
     * @param \SkedApp\CoreBundle\Entity\Consultant $consultants
     */
    public function removeConsultant(\SkedApp\CoreBundle\Entity\Consultant $consultants)
    {
        $this->consultants->removeElement($consultants);
    }

    /**
     * Get consultants
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getConsultants()
    {
        return $this->consultants;
    }
}