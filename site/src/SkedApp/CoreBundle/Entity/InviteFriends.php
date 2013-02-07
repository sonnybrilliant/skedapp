<?php

namespace SkedApp\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * SkedApp\CoreBundle\Entity\InviteFriends
 *
 * @ORM\Entity(repositoryClass="SkedApp\CoreBundle\Repository\InviteFriendsRepository")
 * @ORM\Table(name="invite_friends")
 *
 * @author Ronald Conco <ronald.conco@gmail.com>
 * @package SkedAppCoreBundle
 * @subpackage Entity
 * @version 0.0.1
 */
class InviteFriends
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
     * @Assert\NotBlank(message = "Friend's name cannot be blank!")
     * @Assert\MinLength(limit= 2, message="Friend's name must have at least {{ limit }} characters.")
     * @Assert\MaxLength(limit= 100, message="Friend's name has a limit of {{ limit }} characters.")
     * @Assert\Regex(pattern="/\d/",
     *               match=true,
     *               message="Friend's name cannot contain a number"
     *  )
     *
     * @ORM\Column(name="friend_name", type="string", length=100)
     */
    protected $friendName;
    
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
     * @var Customer
     *
     * @ORM\ManyToOne(targetEntity="SkedApp\CoreBundle\Entity\Customer")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="customer_id", referencedColumnName="id")
     * })
     */
    protected $customer;

    /**
     * @var Consultant
     *
     * @ORM\ManyToOne(targetEntity="SkedApp\CoreBundle\Entity\Consultant")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="consultant_id", referencedColumnName="id")
     * })
     */
    protected $consultant;
    
    /**
     * @var Consultant
     *
     * @ORM\ManyToOne(targetEntity="SkedApp\CoreBundle\Entity\Member")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="member_id", referencedColumnName="id")
     * })
     */
    protected $member;    
    
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
        $this->createdAt = new \DateTime();
        $this->updatedAt = new \DateTime();
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
     * Set friendName
     *
     * @param string $friendName
     * @return InviteFriends
     */
    public function setFriendName($friendName)
    {
        $this->friendName = $friendName;
    
        return $this;
    }

    /**
     * Get friendName
     *
     * @return string 
     */
    public function getFriendName()
    {
        return $this->friendName;
    }

    /**
     * Set email
     *
     * @param string $email
     * @return InviteFriends
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
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return InviteFriends
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
     * @return InviteFriends
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
     * Set customer
     *
     * @param \SkedApp\CoreBundle\Entity\Customer $customer
     * @return InviteFriends
     */
    public function setCustomer(\SkedApp\CoreBundle\Entity\Customer $customer = null)
    {
        $this->customer = $customer;
    
        return $this;
    }

    /**
     * Get customer
     *
     * @return \SkedApp\CoreBundle\Entity\Customer 
     */
    public function getCustomer()
    {
        return $this->customer;
    }

    /**
     * Set consultant
     *
     * @param \SkedApp\CoreBundle\Entity\Consultant $consultant
     * @return InviteFriends
     */
    public function setConsultant(\SkedApp\CoreBundle\Entity\Consultant $consultant = null)
    {
        $this->consultant = $consultant;
    
        return $this;
    }

    /**
     * Get consultant
     *
     * @return \SkedApp\CoreBundle\Entity\Consultant 
     */
    public function getConsultant()
    {
        return $this->consultant;
    }

    /**
     * Set member
     *
     * @param \SkedApp\CoreBundle\Entity\Member $member
     * @return InviteFriends
     */
    public function setMember(\SkedApp\CoreBundle\Entity\Member $member = null)
    {
        $this->member = $member;
    
        return $this;
    }

    /**
     * Get member
     *
     * @return \SkedApp\CoreBundle\Entity\Member 
     */
    public function getMember()
    {
        return $this->member;
    }
}