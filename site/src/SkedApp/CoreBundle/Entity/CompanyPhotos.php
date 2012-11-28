<?php

namespace SkedApp\CoreBundle\Entity;

use Symfony\Component\Validator\Mapping\ClassMetadata;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints as DoctrineAssert;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * SkedApp\CoreBundle\Entity\Company
 *
 * @ORM\Table(name="company_photos")
 * @ORM\Entity(repositoryClass="SkedApp\CoreBundle\Repository\CompanyPhotosRepository")
 * @ORM\HasLifecycleCallbacks
 *
 * @author Otto Saayman <otto.saayman@kaizania.co.za>
 * @package SkedAppCoreBundle
 * @subpackage Entity
 * @version 0.0.1
 */
class CompanyPhotos
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
    protected $caption;

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
     * @var boolean
     *
     * @ORM\Column(name="is_deleted", type="boolean")
     */
    protected $isDeleted;

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

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $path;

    /**
     * @Assert\File(
     * maxSize="1M",
     * maxSizeMessage= "The file is too large ({{ size }}). Allowed maximum size is {{ limit }}",
     * mimeTypes = {"image/jpeg", "image/jpg"},
     * mimeTypesMessage = "Please upload a valid image file, we current only support jpeg.",
     * uploadErrorMessage = "The file could not be uploaded"
     * )
     */
    public $picture;

    public function __construct()
    {
        $this->setIsLocked(false);
        $this->setIsActive(true);
        $this->isDeleted = false;
    }

    public function __toString()
    {
        return $this->getCaption ();
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

    public function getWebPath()
    {
        return null === $this->path ? null : $this->getUploadDir() . '/' . $this->path;
    }

    protected function getUploadRootDir()
    {
        // the absolute directory path where uploaded documents should be saved
        return __DIR__ . '/../../../../web/' . $this->getUploadDir();
    }

    protected function getUploadDir()
    {
        // get rid of the __DIR__ so it doesn't screw when displaying uploaded doc/image in the view.
        return 'uploads/company_photos';
    }

    /**
     * @ORM\PrePersist()
     * @ORM\PreUpdate()
     */
    public function preUpload()
    {
        if (null !== $this->picture) {
            $this->path = $this->picture->guessExtension();
        }
    }

    /**
     * @ORM\PostPersist()
     * @ORM\PostUpdate()
     */
    public function upload()
    {
        if (null === $this->picture) {
            return;
        }

        // you must throw an exception here if the file cannot be moved
        // so that the entity is not persisted to the database
        // which the UploadedFile move() method does

        $this->picture->move($this->getUploadRootDir(), $this->id . '.' . $this->picture->guessExtension());
        unset($this->picture);
    }

    /**
     * @ORM\PreRemove()
     */
    public function removeUpload()
    {
        if ($picture = $this->getAbsolutePath()) {
            unlink($picture);
        }
    }

    public function getAbsolutePath()
    {
        return null === $this->path ? null : $this->getUploadRootDir() . '/' . $this->id . '.' . $this->path;
    }

    /**
     * Set caption
     *
     * @param string $strCaption
     * @return CompanyPhoto
     */
    public function setCaption ($strCaption)
    {
        $this->caption = $strCaption;

        return $this;
    }

    /**
     * Get caption
     *
     * @return string
     */
    public function getCaption()
    {
        return $this->caption;
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

    /**
     * Set path
     *
     * @param string $path
     * @return Consultant
     */
    public function setPath($path)
    {
        $this->path = $path;

        return $this;
    }

    /**
     * Get path
     *
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Set isDeleted
     *
     * @param boolean $isDeleted
     * @return Consultant
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

}