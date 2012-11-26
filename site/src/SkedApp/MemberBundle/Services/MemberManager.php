<?php

namespace SkedApp\MemberBundle\Services;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Monolog\Logger;
use SkedApp\CoreBundle\Entity\Member;

/**
 * Member manager
 *
 * @author Ronald Conco <ronald.conco@gmail.com>
 * @version 0.0.1
 * @package SkedAppMemberBundle
 * @subpackage Services
 */
final class MemberManager
{

    /**
     * Service Container
     * @var object
     */
    private $container = null;

    /**
     * Monolog logger
     * @var object
     */
    private $logger = null;

    /**
     * Entity manager
     * @var object
     */
    private $em;

    /**
     * Class construct
     *
     * @param  ContainerInterface $container
     * @param  Logger             $logger
     * @return void
     */
    public function __construct(
    ContainerInterface $container, Logger $logger)
    {
        $this->setContainer($container);
        $this->setLogger($logger);
        $this->setEm($container->get('doctrine')->getEntityManager('default'));

        return;
    }

    public function getContainer()
    {
        return $this->container;
    }

    public function setContainer($container)
    {
        $this->container = $container;
    }

    public function getLogger()
    {
        return $this->logger;
    }

    public function setLogger($logger)
    {
        $this->logger = $logger;
    }

    public function getEm()
    {
        return $this->em;
    }

    public function setEm($em)
    {
        $this->em = $em;
    }

    /**
     * Get member by id
     * @param integer $id
     * @return SkedAppCoreBundle:Member
     * @throws \Exception
     */
    public function getById($id)
    {
        $member = $this->em->getRepository('SkedAppCoreBundle:Member')
            ->find($id);

        if (!$member) {
            throw new \Exception('Member not found for id:' . $id);
            $this->logger->err('Failed to find member by id:' . $id);
        }

        return $member;
    }

    /**
     * Get all agencies query
     *
     * @param array $options
     * @return query
     */
    public function listAll($options = array())
    {
        return $this->em
                ->getRepository('SkedAppCoreBundle:Member')
                ->getAllMembersQuery($options);
    }

    /**
     * Create a new member
     *
     * @param \SkedApp\CoreBundle\Entities\Member $member
     * @return void
     */
    public function createNewMember($member)
    {
        //set status
        $member->setStatus($this->container->get('status.manager')->active());

        //get user
        $securityContext = $this->container->get('security.context');
        $user = $securityContext->getToken()->getUser();
        $member->setCreatedBy($user);

        //assign roles
        $group = $this->em->getRepository('SkedAppCoreBundle:Group')->find($member->getGroup()->getId());

        foreach ($group->getRoles() as $role) {
            $member->addMemberRole($role);
        }

        $this->em->persist($member);
        $this->em->flush();
        return;
    }

    /**
     * Update member
     *
     * @param \SkedApp\CoreBundle\Entities\Member $member
     * @return void
     */
    public function updateMember($member)
    {
        //reset roles
        $member->getMemberRoles()->clear();
        //assign roles
        $group = $this->em->getRepository('SkedAppCoreBundle:Group')->find($member->getGroup()->getId());

        foreach ($group->getRoles() as $role) {
            $member->addMemberRole($role);
        }

        $this->em->persist($member);
        $this->em->flush();
        return;
    }

    /**
     * Update member object
     */
    public function update($member)
    {
        $this->em->persist($member);
        $this->em->flush();
        return;
    }

    /**
     * Delete member
     *
     * @param \SkedApp\CoreBundle\Entities\Member $member
     * @return void
     */
    public function deleteMember($member)
    {
        $member->setStatus($this->container->get('status.manager')->deleted());
        $member->setIsDeleted(true);
        $member->setEnabled(false);

        $this->em->persist($member);
        $this->em->flush();
        return;
    }

    /**
     * Get member by email address
     *
     * @param string $email
     * @return boolean
     */
    public function getByEmail($email)
    {
        $members = $this->em->getRepository('SkedAppCoreBundle:Member')
            ->findByEmail($email);

        if ($members) {
            return $members[0];
        }
        return false;
    }

    /**
     * Get member by token
     * @param string $token
     * @return boolean
     */
    public function getByToken($token)
    {
        $members = $this->em->getRepository('SkedAppCoreBundle:Member')
            ->findByConfirmationToken($token);

        if ($members) {
            return $members[0];
        }
        return false;
    }

    /**
     * Generate password
     *
     * @param integer $length
     * @param integer $use_upper
     * @param integer $use_lower
     * @param integer $use_number
     * @param integer $use_custom
     * @return string
     */
    public function generatePassword($length = 8, $use_upper = 1, $use_lower = 1, $use_number = 1, $use_custom = "")
    {
        $upper = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
        $lower = "abcdefghijklmnopqrstuvwxyz";
        $number = "0123456789";
        $seed_length = null;
        $seed = null;
        $password = null;

        if ($use_upper) {
            $seed_length += 26;
            $seed .= $upper;
        }
        if ($use_lower) {
            $seed_length += 26;
            $seed .= $lower;
        }
        if ($use_number) {
            $seed_length += 10;
            $seed .= $number;
        }
        if ($use_custom) {
            $seed_length +=strlen($use_custom);
            $seed .= $use_custom;
        }
        for ($x = 1; $x <= $length; $x++) {
            $password .= $seed{rand(0, $seed_length - 1)};
        }
        return($password);
    }

}
