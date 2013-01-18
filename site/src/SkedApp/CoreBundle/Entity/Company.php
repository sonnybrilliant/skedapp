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
 * @ORM\Table(name="company")
 * @ORM\Entity(repositoryClass="SkedApp\CoreBundle\Repository\CompanyRepository")
 * @ORM\HasLifecycleCallbacks
 *
 * @DoctrineAssert\UniqueEntity(fields={"name"}, message="Service provider name must be unique, please choose another name.")
 *
 * @author Ronald Conco <ronald.conco@gmail.com>
 * @package SkedAppCoreBundle
 * @subpackage Entity
 * @version 0.0.1
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
     * @Assert\NotBlank(message = "Service provider name cannot be blank!")
     * @Assert\MinLength(limit= 2, message="Service provider name must have at least {{ limit }} characters.")
     * @Assert\MaxLength(limit= 100, message="Service provider name has a limit of {{ limit }} characters.")
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
     * @var string $address
     *
     * @Assert\NotBlank(message = "Service provider address cannot be blank!")
     * @Assert\MinLength(limit= 2, message="Service provider address must have at least {{ limit }} characters.")
     * @Assert\MaxLength(limit= 150, message="Service provider address has a limit of {{ limit }} characters.")
     *
     * @ORM\Column(name="address", type="string", length=150, nullable=false)
     */
    protected $address;

    /**
     * @var string
     *
     * @Assert\Type(type="numeric", message="Contact number {{ value }} is not a valid {{ type }} mobile number.")
     * @Assert\MinLength(limit= 10, message="Contactnumber must have at least {{ limit }} characters.")
     * @Assert\MaxLength(limit= 12, message="Contact number has a limit of {{ limit }} characters.")
     *
     * @ORM\Column(name="contact_number", type="string", length=12 , nullable=true)
     */
    protected $contactNumber;

    /**
     * @var string $locality
     *
     * @Assert\NotBlank(message = "Service provider locality cannot be blank!")
     * @Assert\MinLength(limit= 2, message="Service provider locality must have at least {{ limit }} characters.")
     * @Assert\MaxLength(limit= 150, message="Service provider locality has a limit of {{ limit }} characters.")
     *
     * @ORM\Column(name="locality", type="string", length=150, nullable=true)
     */
    protected $locality;

    /**
     * @var string $country
     *
     * @Assert\NotBlank(message = "Service provider country cannot be blank!")
     * @Assert\MinLength(limit= 2, message="Service provider country must have at least {{ limit }} characters.")
     * @Assert\MaxLength(limit= 150, message="Service provider country has a limit of {{ limit }} characters.")
     *
     * @ORM\Column(name="country", type="string", length=150, nullable=true)
     */
    protected $country;

    /**
     * @var string $lat
     *
     * @Assert\NotBlank(message = "Service provider lat cannot be blank!")
     * @Assert\MinLength(limit= 2, message="Service provider lat must have at least {{ limit }} characters.")
     * @Assert\MaxLength(limit= 50, message="Service provider lat has a limit of {{ limit }} characters.")
     *
     * @ORM\Column(name="lat", type="string", length=50, nullable=true)
     */
    protected $lat;

    /**
     * @var string $lng
     *
     * @Assert\NotBlank(message = "Service provider lng cannot be blank!")
     * @Assert\MinLength(limit= 2, message="Service provider lng must have at least {{ limit }} characters.")
     * @Assert\MaxLength(limit= 50, message="Service provider lng has a limit of {{ limit }} characters.")
     *

     * @ORM\Column(name="lng", type="string", length=50, nullable=true)
     */
    protected $lng;

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

    /**
     * @ORM\OneToMany(targetEntity="SkedApp\CoreBundle\Entity\Member", mappedBy="company")
     */
    protected $members;

    /**
     * @ORM\OneToMany(targetEntity="SkedApp\CoreBundle\Entity\Consultant", mappedBy="company")
     */
    protected $consultants;

    /**
     * @ORM\OneToMany(targetEntity="SkedApp\CoreBundle\Entity\CompanyPhotos", mappedBy="company")
     */
    protected $photos;

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
    protected $isDeleted = 0;

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

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $path;

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
        return 'uploads/companies';
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
     * Set address
     *
     * @param string $address
     */
    public function setAddress($address)
    {
        $this->address = $address;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * Set locality
     *
     * @param string $locality
     */
    public function setLocality($locality)
    {
        $this->locality = $locality;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getLocality()
    {
        return $this->locality;
    }

    /**
     * Set country
     *
     * @param string $country
     */
    public function setCountry($country)
    {
        $this->country = $country;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * Set lat
     *
     * @param string $lat
     */
    public function setLat($lat)
    {
        $this->lat = $lat;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getLat()
    {
        return $this->lat;
    }

    /**
     * Set lng
     *
     * @param string $lng
     */
    public function setLng($lng)
    {
        $this->lng = $lng;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getLng()
    {
        return $this->lng;
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

    /**
     * Add photos
     *
     * @param \SkedApp\CoreBundle\Entity\CompanyPhotos $photos
     * @return Company
     */
    public function addPhoto (\SkedApp\CoreBundle\Entity\CompanyPhotos $photos)
    {
        $this->photos[] = $photos;

        return $this;
    }

    /**
     * Remove photos
     *
     * @param \SkedApp\CoreBundle\Entity\CompanyPhotos $photos
     */
    public function removePhoto (\SkedApp\CoreBundle\Entity\CompanyPhotos $photos)
    {
        $this->photos->removeElement($photos);
    }

    /**
     * Get photos
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getPhotos()
    {
        return $this->photos;
    }

    /**
     * Set path
     *
     * @param string $path
     * @return Company
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

        if (is_null ($isDeleted))
                $isDeleted = 0;

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

        if (is_null ($this->isDeleted))
                $this->isDeleted = 0;

        return $this->isDeleted;
    }


    /**
     * Set contactNumber
     *
     * @param string $contactNumber
     * @return Company
     */
    public function setContactNumber($contactNumber)
    {
        $this->contactNumber = $contactNumber;

        return $this;
    }

    /**
     * Get contactNumber
     *
     * @return string
     */
    public function getContactNumber()
    {
        return $this->contactNumber;
    }

    /**
     * Get full Address in one line
     *
     * @return string
     */
    public function getAddressInLine()
    {

        $return_string = '';

        if (strlen ($this->address) > 0)
                $return_string .= $this->address;

        if (strlen ($this->locality) > 0) {

            if (strlen ($return_string) > 0)
                    $return_string .= ', ';

            $return_string .= $this->locality;

        }

        if (strlen ($this->country) > 0) {

            if (strlen ($return_string) > 0)
                    $return_string .= ', ';

            $return_string .= $this->country;

        }

        return $return_string;

    }


    /**
     * Get GPS co-ordinates in human friendly format from a given decimal co-ordinate
     *
     * @return array
     */
    public function getDegressStringFromDecimal ($decimalGPS) {

        $vars = explode(".",$decimalGPS);

        $deg = $vars[0];
        $tempma = "0.".$vars[1];

        $tempma = $tempma * 3600;
        $min = floor($tempma / 60);
        $sec = $tempma - ($min*60);

        return array("deg"=>$deg, "min"=>$min, "sec"=>$sec);

    }

    /**
     * Get GPS co-ordinates in human friendly format
     *
     * @return string
     */
    public function getCompleteGPS()
    {

        $return_string = '';

        $latitudeArray = $this->getDegressStringFromDecimal($this->getLat());
        $longitudeArray = $this->getDegressStringFromDecimal($this->getLng());

        $return_string = abs($latitudeArray['deg']) . '° ' . $latitudeArray['min'] . "' " . round($latitudeArray['sec'], 3) . '"';

        if ($this->getLat() < 0) {
          $return_string .= ' S ';
        } elseif ($this->getLat() > 0) {
          $return_string .= ' N ';
        }

        $return_string .= abs($longitudeArray['deg']) . '° ' . $longitudeArray['min'] . "' " . round($longitudeArray['sec'], 3) . '"';

        if ($this->getLng() < 0) {
          $return_string .= ' W';
        } elseif ($this->getLng() > 0) {
          $return_string .= ' E';
        }

        return $return_string;

    }

    public function getObjectAsArray ()
    {
        return array (
            'id' => $this->getId(),
            'name' => $this->getName(),
            'description' => $this->getDescription(),
            'address' => $this->getAddress(),
            'contact_number' => $this->getContactNumber(),
            'locality' => $this->getLocality(),
            'country' => $this->getCountry(),
            'lat' => $this->getLat(),
            'lng' => $this->getLng(),
            'is_active' => $this->getIsActive(),
            'is_deleted' => $this->getIsDeleted(),
            'is_locked' => $this->getIsLocked(),
        );
    }

}