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
 * SkedApp\CoreBundle\Entity\CustomerPotential
 *
 * @ORM\Table(name="customer_potential")
 * @ORM\Entity(repositoryClass="SkedApp\CoreBundle\Repository\CustomerPotentialRepository")
 * @ORM\HasLifecycleCallbacks
 *
 * @author Otto Saayman <otto.saayman@creativecloud.co.za>
 * @package SkedAppCoreBundle
 * @subpackage Entity
 * @version 0.0.1
 */
class CustomerPotential implements \Serializable
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
     * @var Gender
     *
     * @ORM\ManyToOne(targetEntity="SkedApp\CoreBundle\Entity\Gender")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="gender_id", referencedColumnName="id")
     * })
     */
    protected $gender;

    /**
     * @var string
     *
     * @Assert\Type(type="numeric", message="Mobile number {{ value }} is not a valid {{ type }} mobile number.")
     * @Assert\MinLength(limit= 10, message="Mobile number must have at least {{ limit }} characters.")
     * @Assert\MaxLength(limit= 12, message="Mobile number has a limit of {{ limit }} characters.")
     *
     * @ORM\Column(name="mobile_number", type="string", length=12 , nullable=true)
     */
    protected $mobileNumber;

    /**
     * @var string
     *
     * @Assert\Type(type="numeric", message="Land line number {{ value }} is not a valid {{ type }} land line number.")
     * @Assert\MinLength(limit= 10, message="Land line number must have at least {{ limit }} characters.")
     * @Assert\MaxLength(limit= 12, message="Land line number has a limit of {{ limit }} characters.")
     *
     * @ORM\Column(name="land_line_number", type="string", length=12 , nullable=true)
     */
    protected $landLineNumber;

    /**
     * @ORM\OneToMany(targetEntity="SkedApp\CoreBundle\Entity\Booking", mappedBy="customerPotential")
     */
    protected $bookings;

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
     * @var datetime $expiresAt
     *
     * @ORM\Column(name="expires_at", type="datetime", nullable=true)
     */
    protected $expiresAt;

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
        $this->enabled = false;
        $this->isActive = false;
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
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @ORM\PrePersist()
     */
    public function finalizeMember()
    {
        if (null == $this->getExpiresAt()) {
            $date = new \DateTime();
            $this->setExpiresAt($date->modify('+6 months'));
        }
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

    public function isEnabled()
    {
        return $this->enabled;
    }

    /**
     * Set firstName
     *
     * @param string $firstName
     * @return Customer
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
     * @return Customer
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
     * Set email
     *
     * @param string $email
     * @return Customer
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
     * Set mobileNumber
     *
     * @param string $mobileNumber
     * @return Customer
     */
    public function setMobileNumber($mobileNumber)
    {
        $this->mobileNumber = $mobileNumber;

        return $this;
    }

    /**
     * Get mobileNumber
     *
     * @return string
     */
    public function getMobileNumber()
    {
        return $this->mobileNumber;
    }

    /**
     * Set landLineNumber
     *
     * @param string $landLineNumber
     * @return Customer
     */
    public function setLandLineNumber($landLineNumber)
    {
        $this->landLineNumber = $landLineNumber;

        return $this;
    }

    /**
     * Get landLineNumber
     *
     * @return string
     */
    public function getLandLineNumber()
    {
        return $this->landLineNumber;
    }

    /**
     * Set enabled
     *
     * @param boolean $enabled
     * @return Customer
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
     * @return Customer
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
     * Set expiresAt
     *
     * @param \DateTime $expiresAt
     * @return Customer
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
     * Set isActive
     *
     * @param boolean $isActive
     * @return Customer
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
     * Set isDeleted
     *
     * @param boolean $isDeleted
     * @return Customer
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
     * Set isLocked
     *
     * @param boolean $isLocked
     * @return Customer
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
     * @return Customer
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
     * @return Customer
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
     * Add bookings
     *
     * @param \SkedApp\CoreBundle\Entity\Booking $bookings
     * @return Customer
     */
    public function addBooking(\SkedApp\CoreBundle\Entity\Booking $bookings)
    {
        $this->bookings[] = $bookings;

        return $this;
    }

    /**
     * Remove bookings
     *
     * @param \SkedApp\CoreBundle\Entity\Booking $bookings
     */
    public function removeBooking(\SkedApp\CoreBundle\Entity\Booking $bookings)
    {
        $this->bookings->removeElement($bookings);
    }

    /**
     * Get bookings
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getBookings()
    {
        return $this->bookings;
    }

    /**
     * Get full name of customer
     *
     * @return string
     */
    public function getFullName ()
    {
        return $this->firstName.' '.$this->lastName;
    }

    public function getObjectAsArray ()
    {
        return array (
            'id' => $this->getId(),
            'first_name' => $this->getFirstName(),
            'last_name' => $this->getLastName(),
            'email' => $this->getEmail(),
            'username' => $this->getUsername(),
            'mobile_number' => $this->getMobileNumber(),
            'enabled' => $this->getEnabled(),
            'expired' => $this->getExpired(),
            'last_login' => $this->getLastLogin(),
            'expires_at' => $this->getExpiresAt(),
            'is_active' => $this->getIsActive(),
            'is_deleted' => $this->getIsDeleted(),
            'is_locked' => $this->getIsLocked(),
        );
    }

    /**
     * Set gender
     *
     * @param \SkedApp\CoreBundle\Entity\Gender $gender
     * @return Customer
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
}