<?php

namespace SkedApp\CoreBundle\Entity;

use Symfony\Component\Validator\Mapping\ClassMetadata;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints as DoctrineAssert;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * SkedApp\CoreBundle\Entity\Category
 *
 * @ORM\Entity(repositoryClass="SkedApp\CoreBundle\Repository\CategoryRepository")
 * @ORM\Table(name="category")
 * @ORM\HasLifecycleCallbacks
 * @DoctrineAssert\UniqueEntity(fields={"name"}, message="Company name must be unique, please choose another name.")
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
    protected $id;

    /**
     * @var string
     *
     * @Assert\NotBlank(message = "Category name cannot be blank!")
     * @Assert\MinLength(limit= 3, message="Category name must have at least {{ limit }} characters.")
     * @Assert\MaxLength(limit= 30, message="Category name has a limit of {{ limit }} characters.")
     * 
     * @ORM\Column(name="name", type="string", length=30)
     */
    protected $name;

    /**
     * @var string $description
     * 
     * @Assert\MaxLength(limit= 100, message="Category description has a limit of {{ limit }} characters.")
     * 
     * @ORM\Column(name="description", type="text", length=500, nullable=true)
     */
    protected $description;

    /**
     * @ORM\OneToMany(targetEntity="SkedApp\CoreBundle\Entity\Service", mappedBy="category")
     */
    protected $services;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_deleted", type="boolean")
     */
    protected $isDeleted;

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
        return 'uploads/categories';
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

    public function __construct($name = null)
    {
        $this->name = $name;
        $this->isDeleted = false;
        $this->createdAt = new \DateTime();
        $this->updatedAt = new \DateTime();
    }

    public function __toString()
    {
        return $this->name;
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

    /**
     * Set isDeleted
     *
     * @param boolean $isDeleted
     * @return Category
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
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return Category
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
     * @return Category
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
     * @return Category
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
     * Set path
     *
     * @param string $path
     * @return Category
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

}