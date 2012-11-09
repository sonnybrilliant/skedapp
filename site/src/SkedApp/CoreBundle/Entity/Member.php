<?php

namespace SkedApp\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints as DoctrineAssert;
use Symfony\Component\Security\Core\Encoder\MessageDigestPasswordEncoder;

/**
 * SkedApp\CoreBundle\Entity\Member
 *
 * @ORM\Entity(repositoryClass="SkedApp\CoreBundle\Repository\MemberRepository")
 * @ORM\Table(name="member")
 * @DoctrineAssert\UniqueEntity(fields={"email"}, message="Email address is already being used by another user, please try another one.")
 * @ORM\HasLifecycleCallbacks
 *
 * @author Ronald Conco <ronald.conco@gmail.com>
 * @package SkedAppCoreBundle
 * @subpackage Entity
 * @version 0.0.1
 */
class Member implements UserInterface
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
     * @Assert\NotBlank(message = "First Name cannot be blank!")
     * @Assert\MinLength(limit= 2, message="First Name must have at least {{ limit }} characters.")
     * @Assert\MaxLength(limit= 100, message="First Name has a limit of {{ limit }} characters.")
     * @Assert\Regex(pattern="/\d/",
     *               match=false,
     *               message="First Name cannot contain a number"
     *  )
     *
     * @ORM\Column(name="first_name", type="string", length=100)
     */
    protected $firstName;

    /**
     * @var string
     *
     * @Assert\NotBlank(message = "Last Name cannot be blank!")
     * @Assert\MinLength(limit= 2, message="Last Name must have at least {{ limit }} characters.")
     * @Assert\MaxLength(limit= 100, message="Last Name has a limit of {{ limit }} characters.")
     * @Assert\Regex(pattern="/\d/",
     *               match=false,
     *               message="Last Name cannot contain a number"
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
     * @ORM\JoinTable(name="member_role_map",
     *     joinColumns={@ORM\JoinColumn(name="member_id", referencedColumnName="id")},
     *     inverseJoinColumns={@ORM\JoinColumn(name="role_id", referencedColumnName="id")}
     * )
     */
    protected $memberRoles;

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
     * @var Status
     *
     * @ORM\ManyToOne(targetEntity="SkedApp\CoreBundle\Entity\Status")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="status_id", referencedColumnName="id")
     * })
     */
    protected $status;

    /**
     * @var Status
     *
     * @ORM\ManyToOne(targetEntity="SkedApp\CoreBundle\Entity\Group")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="group_id", referencedColumnName="id")
     * })
     */
    protected $group;

    /**
     * @var Title
     *
     * @ORM\ManyToOne(targetEntity="SkedApp\CoreBundle\Entity\Title")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="title_id", referencedColumnName="id")
     * })
     */
    protected $title;

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
     * @ORM\Column(name="confirmation_token", type="string" , length=25 ,nullable= true)
     */
    protected $confirmationToken;

    /**
     * @var datetime
     *
     * @ORM\Column(name="password_requested_at", type="datetime" , nullable= true)
     */
    protected $passwordRequestedAt;

    /**
     * @var SkedApp\CoreBundle\Entity\Company
     * 
     *
     * @ORM\ManyToOne(targetEntity="SkedApp\CoreBundle\Entity\Company", inversedBy="members")
     * @ORM\JoinColumn(name="company_id", referencedColumnName="id")
     */
    protected $company;    
    
    /**
     * @var datetime
     *
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(name="created_at", type="datetime")
     * @link https://github.com/stof/StofDoctrineExtensionsBundle
     */
    protected $createdAt;

    /**
     * @var datetime
     *
     * @ORM\Column(name="updated_at", type="datetime")
     * @Gedmo\Timestampable(on="update")
     * @link https://github.com/stof/StofDoctrineExtensionsBundle
     */
    protected $updatedAt;

    public function __construct()
    {
        $this->enabled = true;
        $this->expired = false;
        $this->memberRoles = new ArrayCollection();
        $this->createdAt = new \DateTime();
        $this->updatedAt = new \DateTime();
    }

    public function __toString()
    {
        return $this->firstName . ' ' .$this->lastName ;
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
        return $this->getMemberRoles()->toArray();
    }

    /**
     * Compares this user to another to determine if they are the same.
     *
     * @param  UserInterface $user The user
     * @return boolean       True if equal, false othwerwise.
     */
    public function equals(UserInterface $user)
    {
        return md5($this->getUsername()) == md5($user->getUsername());
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
        $encoder = new MessageDigestPasswordEncoder('sha512' ,true ,10);
        $password = $encoder->encodePassword($this->getPassword() ,
                $this->getSalt());
        $this->setPassword($password);
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
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;
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
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;
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
     * Set mobileNumber
     *
     * @param string $mobileNumber
     */
    public function setMobileNumber($mobileNumber)
    {
        $this->mobileNumber = $mobileNumber;
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
     * Set status
     *
     * @param SkedApp\CoreBundle\Entity\Status $status
     */
    public function setStatus(\SkedApp\CoreBundle\Entity\Status $status)
    {
        $this->status = $status;
    }

    /**
     * Get status
     *
     * @return SkedApp\CoreBundle\Entity\Status
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set title
     *
     * @param SkedApp\CoreBundle\Entity\Title $title
     */
    public function setTitle(\SkedApp\CoreBundle\Entity\Title $title)
    {
        $this->title = $title;
    }

    /**
     * Get title
     *
     * @return SkedApp\CoreBundle\Entity\Title
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set gender
     *
     * @param SkedApp\CoreBundle\Entity\Gender $gender
     */
    public function setGender(\SkedApp\CoreBundle\Entity\Gender $gender)
    {
        $this->gender = $gender;
    }

    /**
     * Get gender
     *
     * @return SkedApp\CoreBundle\Entity\Gender
     */
    public function getGender()
    {
        return $this->gender;
    }

    /**
     * Set username
     *
     * @param string $username
     */
    public function setUsername($username)
    {
        $this->username = $username;
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
     */
    public function setPassword($password)
    {
        $this->password = $password;
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
     */
    public function setSalt($salt)
    {
        $this->salt = $salt;
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
     * Add memberRoles
     *
     * @param SkedApp\CoreBundle\EntityRole $memberRoles
     */
    public function addEntityRole(\SkedApp\CoreBundle\EntityRole $memberRoles)
    {
        $this->memberRoles[] = $memberRoles;
    }

    /**
     * Get memberRoles
     *
     * @return Doctrine\Common\Collections\Collection
     */
    public function getMemberRoles()
    {
        return $this->memberRoles;
    }

    /**
     * Set enabled
     *
     * @param boolean $enabled
     */
    public function setEnabled($enabled)
    {
        $this->enabled = $enabled;
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
     */
    public function setExpired($expired)
    {
        $this->expired = $expired;
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
     * @param datetime $lastLogin
     */
    public function setLastLogin($lastLogin)
    {
        $this->lastLogin = $lastLogin;
    }

    /**
     * Get lastLogin
     *
     * @return datetime
     */
    public function getLastLogin()
    {
        return $this->lastLogin;
    }

    /**
     * Set expiresAt
     *
     * @param datetime $expiresAt
     */
    public function setExpiresAt($expiresAt)
    {
        $this->expiresAt = $expiresAt;
    }

    /**
     * Get expiresAt
     *
     * @return datetime
     */
    public function getExpiresAt()
    {
        return $this->expiresAt;
    }

    /**
     * Set confirmationToken
     *
     * @param string $confirmationToken
     */
    public function setConfirmationToken($confirmationToken)
    {
        $this->confirmationToken = $confirmationToken;
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
     * @param datetime $passwordRequestedAt
     */
    public function setPasswordRequestedAt($passwordRequestedAt)
    {
        $this->passwordRequestedAt = $passwordRequestedAt;
    }

    /**
     * Get passwordRequestedAt
     *
     * @return datetime
     */
    public function getPasswordRequestedAt()
    {
        return $this->passwordRequestedAt;
    }

    /**
     * Add memberRoles
     *
     * @param SkedApp\CoreBundle\Entity\Role $memberRoles
     */
    public function addRole(\SkedApp\CoreBundle\Entity\Role $memberRoles)
    {
        $this->memberRoles[] = $memberRoles;
    }

    /**
     * reset memberRoles
     *
     * @param SkedApp\CoreBundle\Entity\Role $memberRoles
     */
    public function resetRoles()
    {
        $this->memberRoles[] = array ();
    }

    /**
     * Set group
     *
     * @param SkedApp\CoreBundle\Entity\Group $group
     */
    public function setGroup(\SkedApp\CoreBundle\Entity\Group $group)
    {
        $this->group = $group;
    }

    /**
     * Get group
     *
     * @return SkedApp\CoreBundle\Entity\Group
     */
    public function getGroup()
    {
        return $this->group;
    }

    /**
     * Set email
     *
     * @param string $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
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
     * Set company
     *
     * @param SkedApp\CoreBundle\Entity\company $company
     */
    public function setCompany(\SkedApp\CoreBundle\Entity\Company $company)
    {
        $this->company = $company;
    }

    /**
     * Get company
     *
     * @return SkedApp\CoreBundle\Entity\Company
     */
    public function getCompany()
    {
        return $this->company;
    }
    

    /**
     * Add memberRoles
     *
     * @param SkedApp\CoreBundle\Entity\Role $memberRoles
     * @return Member
     */
    public function addMemberRole(\SkedApp\CoreBundle\Entity\Role $memberRoles)
    {
        $this->memberRoles[] = $memberRoles;
    
        return $this;
    }

    /**
     * Remove memberRoles
     *
     * @param SkedApp\CoreBundle\Entity\Role $memberRoles
     */
    public function removeMemberRole(\SkedApp\CoreBundle\Entity\Role $memberRoles)
    {
        $this->memberRoles->removeElement($memberRoles);
    }
}