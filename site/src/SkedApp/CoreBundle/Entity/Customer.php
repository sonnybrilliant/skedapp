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
 * SkedApp\CoreBundle\Entity\Customer
 *
 * @ORM\Table(name="customer")
 * @ORM\Entity(repositoryClass="SkedApp\CoreBundle\Repository\CustomerRepository")
 * @ORM\HasLifecycleCallbacks
 *
 * @author Ronald Conco <ronald.conco@gmail.com>
 * @package SkedAppCoreBundle
 * @subpackage Entity
 * @version 0.0.1
 */
class Customer implements AdvancedUserInterface, \Serializable
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
     * @var salt
     *
     * @ORM\Column(name="salt",type="string", length=255)
     */
    protected $salt;

    /**
     * @var ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="SkedApp\CoreBundle\Entity\Role")
     * @ORM\JoinTable(name="customer_role_map",
     *     joinColumns={@ORM\JoinColumn(name="customer_id", referencedColumnName="id")},
     *     inverseJoinColumns={@ORM\JoinColumn(name="role_id", referencedColumnName="id")}
     * )
     */
    protected $customerRoles;

    /**
     * @ORM\OneToMany(targetEntity="SkedApp\CoreBundle\Entity\Booking", mappedBy="customer")
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
     * @var boolean $radio
     *
     * @ORM\Column(name="ad_radio", type="boolean", nullable=true)
     */
    protected $radio;

    /**
     * @var boolean $internet
     *
     * @ORM\Column(name="ad_internet", type="boolean", nullable=true)
     */
    protected $internet;

    /**
     * @var boolean $tv
     *
     * @ORM\Column(name="ad_tv", type="boolean", nullable=true)
     */
    protected $tv;

    /**
     * @var boolean $twitter
     *
     * @ORM\Column(name="ad_twitter", type="boolean", nullable=true)
     */
    protected $twitter;

    /**
     * @var boolean $facebook
     *
     * @ORM\Column(name="ad_facebook", type="boolean", nullable=true)
     */
    protected $facebook;

    /**
     * @var boolean $printedMedia
     *
     * @ORM\Column(name="ad_printed_media", type="boolean", nullable=true)
     */
    protected $printedMedia;

    /**
     * @var boolean $wordOfMouth
     *
     * @ORM\Column(name="add_word_of_mouth", type="boolean", nullable=true)
     */
    protected $wordOfMouth;

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
     */
    protected $captcha;

    public function __construct()
    {
        $this->enabled = false;
        $this->isActive = false;
        $this->expired = false;
        $this->salt = md5(uniqid(null, true));
        $this->setIsLocked(false);
        $this->setIsActive(true);
        $this->isDeleted = false;
        $this->createdAt = new \DateTime();
        $this->updatedAt = new \DateTime();
    }

    public function __toString()
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
        return $this->getCustomerRoles()->toArray();
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
     * Get username
     *
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
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
     * Get salt
     *
     * @return string
     */
    public function getSalt()
    {
        return $this->salt;
    }

    public function getCaptcha()
    {
        return $this->captcha;
    }

    public function setCaptcha($captcha)
    {
        $this->captcha = $captcha;
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
     * Set username
     *
     * @param string $username
     * @return Customer
     */
    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }

    /**
     * Set password
     *
     * @param string $password
     * @return Customer
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
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
     * Set salt
     *
     * @param string $salt
     * @return Customer
     */
    public function setSalt($salt)
    {
        $this->salt = $salt;

        return $this;
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
     * Set lastLogin
     *
     * @param \DateTime $lastLogin
     * @return Customer
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
     * Set confirmationToken
     *
     * @param string $confirmationToken
     * @return Customer
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
     * @return Customer
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
     * Set radio
     *
     * @param boolean $radio
     * @return Customer
     */
    public function setRadio($radio)
    {
        $this->radio = $radio;

        return $this;
    }

    /**
     * Get radio
     *
     * @return boolean
     */
    public function getRadio()
    {
        return $this->radio;
    }

    /**
     * Set internet
     *
     * @param boolean $internet
     * @return Customer
     */
    public function setInternet($internet)
    {
        $this->internet = $internet;

        return $this;
    }

    /**
     * Get internet
     *
     * @return boolean
     */
    public function getInternet()
    {
        return $this->internet;
    }

    /**
     * Set tv
     *
     * @param boolean $tv
     * @return Customer
     */
    public function setTv($tv)
    {
        $this->tv = $tv;

        return $this;
    }

    /**
     * Get tv
     *
     * @return boolean
     */
    public function getTv()
    {
        return $this->tv;
    }

    /**
     * Set twitter
     *
     * @param boolean $twitter
     * @return Customer
     */
    public function setTwitter($twitter)
    {
        $this->twitter = $twitter;

        return $this;
    }

    /**
     * Get twitter
     *
     * @return boolean
     */
    public function getTwitter()
    {
        return $this->twitter;
    }

    /**
     * Set facebook
     *
     * @param boolean $facebook
     * @return Customer
     */
    public function setFacebook($facebook)
    {
        $this->facebook = $facebook;

        return $this;
    }

    /**
     * Get facebook
     *
     * @return boolean
     */
    public function getFacebook()
    {
        return $this->facebook;
    }

    /**
     * Set printedMedia
     *
     * @param boolean $printedMedia
     * @return Customer
     */
    public function setPrintedMedia($printedMedia)
    {
        $this->printedMedia = $printedMedia;

        return $this;
    }

    /**
     * Get printedMedia
     *
     * @return boolean
     */
    public function getPrintedMedia()
    {
        return $this->printedMedia;
    }

    /**
     * Set wordOfMouth
     *
     * @param boolean $wordOfMouth
     * @return Customer
     */
    public function setWordOfMouth($wordOfMouth)
    {
        $this->wordOfMouth = $wordOfMouth;

        return $this;
    }

    /**
     * Get wordOfMouth
     *
     * @return boolean
     */
    public function getWordOfMouth()
    {
        return $this->wordOfMouth;
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
     * Add customerRoles
     *
     * @param \SkedApp\CoreBundle\Entity\Role $customerRoles
     * @return Customer
     */
    public function addCustomerRole(\SkedApp\CoreBundle\Entity\Role $customerRoles)
    {
        $this->customerRoles[] = $customerRoles;

        return $this;
    }

    /**
     * Remove customerRoles
     *
     * @param \SkedApp\CoreBundle\Entity\Role $customerRoles
     */
    public function removeCustomerRole(\SkedApp\CoreBundle\Entity\Role $customerRoles)
    {
        $this->customerRoles->removeElement($customerRoles);
    }

    /**
     * Get customerRoles
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCustomerRoles()
    {
        return $this->customerRoles;
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