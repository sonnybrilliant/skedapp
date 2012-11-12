<?php
namespace SkedApp\CoreBundle\Entity ;

use Symfony\Component\Validator\Mapping\ClassMetadata;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints as DoctrineAssert;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * SkedApp\CoreBundle\Entity\Company
 *
 * @ORM\Table(name="consultant")
 * @ORM\Entity(repositoryClass="SkedApp\CoreBundle\Repository\consultantRepository")
 *
 */
class Consultant
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
     * @var string
     *
     * @Assert\NotBlank(message = "First name cannot be blank!")
     * @Assert\MinLength(limit= 2, message="First name must have at least {{ limit }} characters.")
     * @Assert\MaxLength(limit= 100, message="First name has a limit of {{ limit }} characters.")
     * @Assert\Regex(pattern="/\d/",
     *               match=false,
     *               message="First name cannot contain a number"
     *  )
     *
     * @ORM\Column(name="first_name", type="string", length=100)
     */
    protected $firstName;

    /**
     * @var string
     *
     * @Assert\NotBlank(message = "Last name cannot be blank!")
     * @Assert\MinLength(limit= 2, message="Last name must have at least {{ limit }} characters.")
     * @Assert\MaxLength(limit= 100, message="Last name has a limit of {{ limit }} characters.")
     * @Assert\Regex(pattern="/\d/",
     *               match=false,
     *               message="Last name cannot contain a number"
     *  )
     *
     * @ORM\Column(name="last_name", type="string", length=100)
     */
    protected $lastName;
    
    /**
     * @var Gender
     *
     * @ORM\ManyToOne(targetEntity="SkedApp\CoreBundle\Entity\Gender")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="gender_id", referencedColumnName="id")
     * })
     */
    protected $gender;    

    /**
     * @var string $speciality
     * 
     * @ORM\Column(name="speciality", type="string", length=254, nullable=true)
     */
    protected $speciality;
    
    /**
     * @var string $professionalStatement
     * 
     * @ORM\Column(name="professional_statement", type="string", length=254, nullable=true)
     */
    protected $professionalStatement;    
    
    /**
     * @var ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="SkedApp\CoreBundle\Entity\Service")
     * @ORM\JoinTable(name="consultant_service_map",
     *     joinColumns={@ORM\JoinColumn(name="consultant_id", referencedColumnName="id")},
     *     inverseJoinColumns={@ORM\JoinColumn(name="service_id", referencedColumnName="id")}
     * )
     */
    protected $consultantServices;      

    /**
     * @var SkedApp\CoreBundle\Entity\Company
     * 
     *
     * @ORM\ManyToOne(targetEntity="SkedApp\CoreBundle\Entity\Company", inversedBy="consultants")
     * @ORM\JoinColumn(name="company_id", referencedColumnName="id")
     */
    protected $company;       
    
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
        $this->setIsLocked(false);
        $this->setIsActive(true);
    }

    public function __toString()
    {
        return $this->getFirstName().' '.$this->getLastName();
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
     * Set firstName
     *
     * @param string $firstName
     * @return Consultant
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;
    
        return $this;
    }

    /**
     * Get firstName
     *
     * @return string 
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * Set lastName
     *
     * @param string $lastName
     * @return Consultant
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;
    
        return $this;
    }

    /**
     * Get lastName
     *
     * @return string 
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * Set speciality
     *
     * @param string $speciality
     * @return Consultant
     */
    public function setSpeciality($speciality)
    {
        $this->speciality = $speciality;
    
        return $this;
    }

    /**
     * Get speciality
     *
     * @return string 
     */
    public function getSpeciality()
    {
        return $this->speciality;
    }

    /**
     * Set professionalStatement
     *
     * @param string $professionalStatement
     * @return Consultant
     */
    public function setProfessionalStatement($professionalStatement)
    {
        $this->professionalStatement = $professionalStatement;
    
        return $this;
    }

    /**
     * Get professionalStatement
     *
     * @return string 
     */
    public function getProfessionalStatement()
    {
        return $this->professionalStatement;
    }

    /**
     * Set isActive
     *
     * @param boolean $isActive
     * @return Consultant
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
     * Set isLocked
     *
     * @param boolean $isLocked
     * @return Consultant
     */
    public function setIsLocked($isLocked)
    {
        $this->isLocked = $isLocked;
    
        return $this;
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
     * @param \DateTime $createdAt
     * @return Consultant
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
     * @return Consultant
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
     * Add consultantServices
     *
     * @param SkedApp\CoreBundle\Entity\Service $consultantServices
     * @return Consultant
     */
    public function addConsultantService(\SkedApp\CoreBundle\Entity\Service $consultantServices)
    {
        $this->consultantServices[] = $consultantServices;
    
        return $this;
    }

    /**
     * Remove consultantServices
     *
     * @param SkedApp\CoreBundle\Entity\Service $consultantServices
     */
    public function removeConsultantService(\SkedApp\CoreBundle\Entity\Service $consultantServices)
    {
        $this->consultantServices->removeElement($consultantServices);
    }

    /**
     * Get consultantServices
     *
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getConsultantServices()
    {
        return $this->consultantServices;
    }

    /**
     * Set gender
     *
     * @param \SkedApp\CoreBundle\Entity\Gender $gender
     * @return Consultant
     */
    public function setGender(\SkedApp\CoreBundle\Entity\Gender $gender = null)
    {
        $this->gender = $gender;
    
        return $this;
    }

    /**
     * Get gender
     *
     * @return \SkedApp\CoreBundle\Entity\Gender 
     */
    public function getGender()
    {
        return $this->gender;
    }

    /**
     * Set company
     *
     * @param \SkedApp\CoreBundle\Entity\Company $company
     * @return Consultant
     */
    public function setCompany(\SkedApp\CoreBundle\Entity\Company $company = null)
    {
        $this->company = $company;
    
        return $this;
    }

    /**
     * Get company
     *
     * @return \SkedApp\CoreBundle\Entity\Company 
     */
    public function getCompany()
    {
        return $this->company;
    }
}