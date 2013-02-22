<?php

namespace SkedApp\CoreBundle\Entity;

use Symfony\Component\Validator\Mapping\ClassMetadata;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints as DoctrineAssert;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Security\Core\Encoder\MessageDigestPasswordEncoder;
use Symfony\Component\Security\Core\User\AdvancedUserInterface;

/**
 * SkedApp\CoreBundle\Entity\Consultant
 *
 * @ORM\Table(name="consultant")
 * @ORM\Entity(repositoryClass="SkedApp\CoreBundle\Repository\ConsultantRepository")
 * @ORM\HasLifecycleCallbacks
 *
 * @author Ronald Conco <ronald.conco@gmail.com>
 * @package SkedAppCoreBundle
 * @subpackage Entity
 * @version 0.0.1
 */
class Consultant implements AdvancedUserInterface, \Serializable
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
     * @var string
     *
     *
     * @Assert\NotBlank(message = "Emailaddress cannot be blank!")
     * @Assert\Email(
     *   message = "The email '{{ value }}' is not a valid email.",
     *   checkMX = false
     * )
     * @ORM\Column(name="email", type="string", length=254)
     */
    protected $email;

    /**
     * @var string
     *
     * @ORM\Column(name="username", type="string", length=255)
     */
    protected $username;

    /**
     * @var string
     *
     * @Assert\NotBlank(message = "Password cannot be blank!")
     * @Assert\MinLength(limit= 5, message="Password must have at least {{ limit }} characters.")
     *
     * @ORM\Column(name="password", type="string", length=255)
     */
    protected $password;

    /**
     * @var salt
     *
     * @ORM\Column(name="salt",type="string", length=255)
     */
    protected $salt;

    /**
     * @var ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="SkedApp\CoreBundle\Entity\Role")
     * @ORM\JoinTable(name="consultant_role_map",
     *     joinColumns={@ORM\JoinColumn(name="consultant_id", referencedColumnName="id")},
     *     inverseJoinColumns={@ORM\JoinColumn(name="role_id", referencedColumnName="id")}
     * )
     */
    protected $consultantRoles;

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
     * @var boolean
     *
     * @ORM\Column(name="enabled", type="boolean")
     */
    protected $enabled;

    /**
     * @var boolean
     *
     * @ORM\Column(name="expired", type="boolean")
     */
    protected $expired;

    /**
     * @var datetime
     *
     * @ORM\Column(name="last_login", type="datetime" , nullable= true)
     */
    protected $lastLogin;

    /**
     * @var datetime
     *
     * @ORM\Column(name="expires_at", type="datetime" , nullable= true)
     */
    protected $expiresAt;

    /**
     * @var string
     *
     * @ORM\Column(name="confirmation_token", type="string" , length=100 ,nullable= true)
     */
    protected $confirmationToken;

    /**
     * @var datetime
     *
     * @ORM\Column(name="password_requested_at", type="datetime" , nullable= true)
     */
    protected $passwordRequestedAt;

    /**
     * @var string $speciality
     *
     * @ORM\Column(name="speciality", type="text", length=10000, nullable=true)
     */
    protected $speciality;

    /**
     * @var string $professionalStatement
     *
     * @ORM\Column(name="professional_statement", type="text", length=10000, nullable=true)
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
     * @var boolean
     *
     * @ORM\Column(name="is_deleted", type="boolean")
     */
    protected $isDeleted;

    /**
     * @var boolean $isLocked
     *
     * @ORM\Column(name="is_locked", type="boolean", nullable=false)
     */
    protected $isLocked;

    /**
     * @var boolean $monday
     *
     * @ORM\Column(name="monday", type="boolean", nullable=false)
     */
    protected $monday;

    /**
     * @var boolean $tuesday
     *
     * @ORM\Column(name="tuesday", type="boolean", nullable=false)
     */
    protected $tuesday;

    /**
     * @var boolean $wednesday
     *
     * @ORM\Column(name="wednesday", type="boolean", nullable=false)
     */
    protected $wednesday;

    /**
     * @var boolean $thursday
     *
     * @ORM\Column(name="thursday", type="boolean", nullable=false)
     */
    protected $thursday;

    /**
     * @var boolean $friday
     *
     * @ORM\Column(name="friday", type="boolean", nullable=false)
     */
    protected $friday;

    /**
     * @var boolean $saturday
     *
     * @ORM\Column(name="saturday", type="boolean", nullable=false)
     */
    protected $saturday;

    /**
     * @var boolean $sunday
     *
     * @ORM\Column(name="sunday", type="boolean", nullable=false)
     */
    protected $sunday;

    /**
     * @var Timeslot
     *
     * @ORM\ManyToOne(targetEntity="SkedApp\CoreBundle\Entity\Timeslots")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="start_time_slot_id", referencedColumnName="id")
     * })
     */
    protected $startTimeslot;

    /**
     * @var Timeslot
     *
     * @ORM\ManyToOne(targetEntity="SkedApp\CoreBundle\Entity\Timeslots")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="end_time_slot_id", referencedColumnName="id")
     * })
     */
    protected $endTimeslot;

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
     * @var string
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $path;

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
     * @Assert\File(
     * maxSize="1M",
     * maxSizeMessage= "The file is too large ({{ size }}). Allowed maximum size is {{ limit }}",
     * mimeTypes = {"image/jpeg", "image/jpg"},
     * mimeTypesMessage = "Please upload a valid image file, we current only support jpeg.",
     * uploadErrorMessage = "The file could not be uploaded"
     * )
     */
    public $picture;
    public $category = null;
    public $available_slots;
    public $slug;

    public function __construct()
    {
        $this->enabled = true;
        $this->expired = false;
        $this->salt = md5(uniqid(null, true));
        $this->setIsLocked(false);
        $this->setIsActive(true);
        $this->isDeleted = false;
    }

    public function __toString()
    {
        return $this->firstName . ' ' . $this->lastName;
    }

    public function fullName()
    {
        return $this->firstName . ' ' . $this->lastName;
    }

    /**
     * Erases the user credentials.
     */
    public function eraseCredentials()
    {
        
    }

    /**
     * Gets an array of roles.
     *
     * @return array An array of Role objects
     */
    public function getRoles()
    {
        return $this->getConsultantRoles()->toArray();
    }

    /**
     * Compares this user to another to determine if they are the same.
     *
     * @param  AdvancedUserInterface $user The user
     * @return boolean       True if equal, false othwerwise.
     */
    public function equals(AdvancedUserInterface $user)
    {
        return md5($this->getUsername()) == md5($user->getUsername());
    }
    
    /**
     * Create slug
     * 
     * @param string $text
     * @return string
     */
    public function slugify($text)
    {
        // replace all non letters or digits by -
        $text = preg_replace('/\W+/', '-', $text);

        // trim and lowercase
        $text = strtolower(trim($text, '-'));

        return $text;
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
        return 'uploads/consultants';
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
     * @ORM\PrePersist()
     */
    public function finalizeMember()
    {
        if (null == $this->getUsername()) {
            $this->setUsername($this->getEmail());
        }

        if (null == $this->getExpiresAt()) {
            $date = new \DateTime();
            $this->setExpiresAt($date->modify('+6 months'));
        }
    }

    /**
     * @ORM\PrePersist()
     */
    public function encodePassword()
    {
        //set password encoding
        $this->setSalt(md5(time()));
        $encoder = new MessageDigestPasswordEncoder('sha512', true, 10);
        $password = $encoder->encodePassword($this->getPassword(), $this->getSalt());
        $this->setPassword($password);
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

    /**
     * Set monday
     *
     * @param boolean $monday
     * @return Consultant
     */
    public function setMonday($monday)
    {
        $this->monday = $monday;

        return $this;
    }

    /**
     * Get monday
     *
     * @return boolean
     */
    public function getMonday()
    {
        return $this->monday;
    }

    /**
     * Set tuesday
     *
     * @param boolean $tuesday
     * @return Consultant
     */
    public function setTuesday($tuesday)
    {
        $this->tuesday = $tuesday;

        return $this;
    }

    /**
     * Get tuesday
     *
     * @return boolean
     */
    public function getTuesday()
    {
        return $this->tuesday;
    }

    /**
     * Set wednesday
     *
     * @param boolean $wednesday
     * @return Consultant
     */
    public function setWednesday($wednesday)
    {
        $this->wednesday = $wednesday;

        return $this;
    }

    /**
     * Get wednesday
     *
     * @return boolean
     */
    public function getWednesday()
    {
        return $this->wednesday;
    }

    /**
     * Set thursday
     *
     * @param boolean $thursday
     * @return Consultant
     */
    public function setThursday($thursday)
    {
        $this->thursday = $thursday;

        return $this;
    }

    /**
     * Get thursday
     *
     * @return boolean
     */
    public function getThursday()
    {
        return $this->thursday;
    }

    /**
     * Set friday
     *
     * @param boolean $friday
     * @return Consultant
     */
    public function setFriday($friday)
    {
        $this->friday = $friday;

        return $this;
    }

    /**
     * Get friday
     *
     * @return boolean
     */
    public function getFriday()
    {
        return $this->friday;
    }

    /**
     * Set saturday
     *
     * @param boolean $saturday
     * @return Consultant
     */
    public function setSaturday($saturday)
    {
        $this->saturday = $saturday;

        return $this;
    }

    /**
     * Get saturday
     *
     * @return boolean
     */
    public function getSaturday()
    {
        return $this->saturday;
    }

    /**
     * Set sunday
     *
     * @param boolean $sunday
     * @return Consultant
     */
    public function setSunday($sunday)
    {
        $this->sunday = $sunday;

        return $this;
    }

    /**
     * Get sunday
     *
     * @return boolean
     */
    public function getSunday()
    {
        return $this->sunday;
    }

    /**
     * Set startTimeslot
     *
     * @param \SkedApp\CoreBundle\Entity\Timeslots $startTimeslot
     * @return Consultant
     */
    public function setStartTimeslot(\SkedApp\CoreBundle\Entity\Timeslots $startTimeslot = null)
    {
        $this->startTimeslot = $startTimeslot;

        return $this;
    }

    /**
     * Get startTimeslot
     *
     * @return \SkedApp\CoreBundle\Entity\Timeslots
     */
    public function getStartTimeslot()
    {
        return $this->startTimeslot;
    }

    /**
     * Set endTimeslot
     *
     * @param \SkedApp\CoreBundle\Entity\Timeslots $endTimeslot
     * @return Consultant
     */
    public function setEndTimeslot(\SkedApp\CoreBundle\Entity\Timeslots $endTimeslot = null)
    {
        $this->endTimeslot = $endTimeslot;

        return $this;
    }

    /**
     * Get endTimeslot
     *
     * @return \SkedApp\CoreBundle\Entity\Timeslots
     */
    public function getEndTimeslot()
    {
        return $this->endTimeslot;
    }

    /**
     * Set appointmentDuration
     *
     * @param \SkedApp\CoreBundle\Entity\AppointmentDuration $appointmentDuration
     * @return Consultant
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
     * Get getDistanceFromPosition
     *
     * @return decimal in kilometers
     */
    public function getDistanceFromPosition($intPositionLat, $intPositionLong)
    {

        $decOut = (6371 * acos(cos(deg2rad($intPositionLat)) * cos(deg2rad($this->company->getLat())) * cos(deg2rad($this->company->getLng()) - deg2rad($intPositionLong))
                + sin(deg2rad($intPositionLat)) * sin(deg2rad($this->company->getLat()))) );

        return $decOut;
    }

    /**
     * Get getDistanceFromPositionString
     *
     * @return string with formatted distance in kilometers
     */
    public function getDistanceFromPositionString($intPositionLat, $intPositionLong)
    {

        $decDistance = $this->getDistanceFromPosition($intPositionLat, $intPositionLong);

        if ($decDistance < 1) {
            return round(($decDistance * 1000), 2) . ' m';
        } else {
            return round($decDistance, 2) . ' km';
        }
    }

    /**
     * Get getIsAvailable
     *
     * @return boolean - true if the consultant is available
     */
    public function getIsAvailable($objDate, $intTimeSlotID)
    {
        return \SkedApp\BookingBundle\Services\BookingManager::getIsAvailable($this->getId(), $objDate);
    }

    /**
     * Get getAvailableBookingSlots
     *
     * @return array with details of open booking slots
     */
    public function getAvailableBookingSlots()
    {
        return $this->available_slots;
    }

    public function setAvailableBookingSlots($arrAvailableSlots)
    {
        $this->available_slots = $arrAvailableSlots;
    }

    /**
     * Set email
     *
     * @param string $email
     * @return Consultant
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set username
     *
     * @param string $username
     * @return Consultant
     */
    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }

    /**
     * Get username
     *
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Set password
     *
     * @param string $password
     * @return Consultant
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Get password
     *
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Set salt
     *
     * @param string $salt
     * @return Consultant
     */
    public function setSalt($salt)
    {
        $this->salt = $salt;

        return $this;
    }

    /**
     * Get salt
     *
     * @return string
     */
    public function getSalt()
    {
        return $this->salt;
    }

    /**
     * Set enabled
     *
     * @param boolean $enabled
     * @return Consultant
     */
    public function setEnabled($enabled)
    {
        $this->enabled = $enabled;

        return $this;
    }

    /**
     * Get enabled
     *
     * @return boolean
     */
    public function getEnabled()
    {
        return $this->enabled;
    }

    /**
     * Set expired
     *
     * @param boolean $expired
     * @return Consultant
     */
    public function setExpired($expired)
    {
        $this->expired = $expired;

        return $this;
    }

    /**
     * Get expired
     *
     * @return boolean
     */
    public function getExpired()
    {
        return $this->expired;
    }

    /**
     * Set lastLogin
     *
     * @param \DateTime $lastLogin
     * @return Consultant
     */
    public function setLastLogin($lastLogin)
    {
        $this->lastLogin = $lastLogin;

        return $this;
    }

    /**
     * Get lastLogin
     *
     * @return \DateTime
     */
    public function getLastLogin()
    {
        return $this->lastLogin;
    }

    /**
     * Set expiresAt
     *
     * @param \DateTime $expiresAt
     * @return Consultant
     */
    public function setExpiresAt($expiresAt)
    {
        $this->expiresAt = $expiresAt;

        return $this;
    }

    /**
     * Get expiresAt
     *
     * @return \DateTime
     */
    public function getExpiresAt()
    {
        return $this->expiresAt;
    }

    /**
     * Set confirmationToken
     *
     * @param string $confirmationToken
     * @return Consultant
     */
    public function setConfirmationToken($confirmationToken)
    {
        $this->confirmationToken = $confirmationToken;

        return $this;
    }

    /**
     * Get confirmationToken
     *
     * @return string
     */
    public function getConfirmationToken()
    {
        return $this->confirmationToken;
    }

    /**
     * Set passwordRequestedAt
     *
     * @param \DateTime $passwordRequestedAt
     * @return Consultant
     */
    public function setPasswordRequestedAt($passwordRequestedAt)
    {
        $this->passwordRequestedAt = $passwordRequestedAt;

        return $this;
    }

    /**
     * Get passwordRequestedAt
     *
     * @return \DateTime
     */
    public function getPasswordRequestedAt()
    {
        return $this->passwordRequestedAt;
    }

    /**
     * Add consultantRoles
     *
     * @param \SkedApp\CoreBundle\Entity\Role $consultantRoles
     * @return Consultant
     */
    public function addConsultantRole(\SkedApp\CoreBundle\Entity\Role $consultantRoles)
    {
        $this->consultantRoles[] = $consultantRoles;

        return $this;
    }

    /**
     * Remove consultantRoles
     *
     * @param \SkedApp\CoreBundle\Entity\Role $consultantRoles
     */
    public function removeConsultantRole(\SkedApp\CoreBundle\Entity\Role $consultantRoles)
    {
        $this->consultantRoles->removeElement($consultantRoles);
    }

    /**
     * Get consultantRoles
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getConsultantRoles()
    {
        return $this->consultantRoles;
    }

    /**
     * @see \Serializable::serialize()
     */
    public function serialize()
    {
        return serialize(array(
                $this->id,
            ));
    }

    /**
     * @see \Serializable::unserialize()
     */
    public function unserialize($serialized)
    {
        list (
            $this->id,
            ) = unserialize($serialized);
    }

    public function isAccountNonExpired()
    {
        return true;
    }

    public function isAccountNonLocked()
    {
        return true;
    }

    public function isCredentialsNonExpired()
    {
        return true;
    }

    public function isEnabled()
    {
        return $this->enabled;
    }

    /**
     * Get full name of consultant
     *
     * @return string
     */
    public function getFullName()
    {
        return $this->firstName . ' ' . $this->lastName;
    }

    public function getObjectAsArray()
    {
        return array(
            'id' => $this->getId(),
            'gender' => $this->getGender()->getName(),
            'company' => $this->getCompany()->getObjectAsArray(),
            'start_time_slot' => $this->getStartTimeslot()->getSlot(),
            'end_time_slot' => $this->getEndTimeslot()->getSlot(),
            'appointment_duration' => $this->getAppointmentDuration()->getDuration(),
            'first_name' => $this->getFirstName(),
            'last_name' => $this->getLastName(),
            'email' => $this->getEmail(),
            'username' => $this->getUsername(),
            'enabled' => $this->getEnabled(),
            'expired' => $this->getExpired(),
            'last_login' => $this->getLastLogin(),
            'expires_at' => $this->getExpiresAt(),
            'speciality' => $this->getSpeciality(),
            'professional_statement' => $this->getProfessionalStatement(),
            'is_active' => $this->getIsActive(),
            'is_deleted' => $this->getIsDeleted(),
            'is_locked' => $this->getIsLocked(),
            'monday' => $this->getMonday(),
            'tuesday' => $this->getTuesday(),
            'wednesday' => $this->getWednesday(),
            'thursday' => $this->getThursday(),
            'friday' => $this->getFriday(),
            'saturday' => $this->getSaturday(),
            'sunday' => $this->getSunday(),
        );
    }


    /**
     * Get slug
     *
     * @return string 
     */
    public function getSlug()
    {
        $fullName = $this->getFullName();
        $this->slug = $this->slugify($fullName);
        return $this->slug;
    }
}