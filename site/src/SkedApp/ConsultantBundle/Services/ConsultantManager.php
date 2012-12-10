<?php

namespace SkedApp\ConsultantBundle\Services;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Monolog\Logger;
use SkedApp\CoreBundle\Entity\Consultant;

/**
 * Consultant manager
 *
 * @author Ronald Conco <ronald.conco@kaizania.com>
 * @package SkedAppServiceBundle
 * @subpackage Consultant
 * @version 0.0.1
 */
final class ConsultantManager
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
     * Get consultant by id
     * 
     * @param integer $id
     * @return SkedAppCoreBundle:Consultant
     * @throws \Exception
     */
    public function getById($id)
    {
        $consultant = $this->em->getRepository('SkedAppCoreBundle:Consultant')
            ->find($id);

        if (!$consultant) {
            throw new \Exception('Consultant not found for id:' . $id);
            $this->logger->err('Failed to find Consultant by id:' . $id);
        }

        return $consultant;
    }

    /**
     * Get consultant by email address
     *
     * @param string $email
     * @return boolean
     */
    public function getByEmail($email)
    {
        $consultants = $this->em->getRepository('SkedAppCoreBundle:Consultant')
            ->findByEmail($email);

        if ($consultants) {
            return $consultants[0];
        }
        return false;
    }

    /**
     * Get consultant by token
     * 
     * @param string $token
     * @return boolean
     */
    public function getByToken($token)
    {
        $consultants = $this->em->getRepository('SkedAppCoreBundle:Consultant')
            ->findByConfirmationToken($token);

        if ($consultants) {
            return $consultants[0];
        }
        return false;
    }

    /** Create default system consultant
     *
     * @param array $params
     * @return SkedAppCoreBudle:Consultant
     */
    public function createDefaultConsultant($params)
    {

        $consultant = new Consultant();
        $consultant->setGender($params['gender']);
        $consultant->setCompany($params['company']);
        $consultant->setStartTimeSlot($params['startTimeSlot']);
        $consultant->setEndTimeSlot($params['endTimeSlot']);
        $consultant->setAppointmentDuration($params['appointmentDuration']);
        $consultant->setFirstName($params['firstName']);
        $consultant->setLastName($params['lastName']);
        $consultant->setEmail($params['email']);
        $consultant->setUsername($params['username']);
        $consultant->setPassword($params['password']);
        $consultant->setEnabled($params['enabled']);
        $consultant->setExpired($params['expired']);
        $consultant->setIsActive($params['isActive']);
        $consultant->setIsDeleted($params['isDeleted']);
        $consultant->setMonday($params['monday']);
        $consultant->setTuesday($params['tuesday']);
        $consultant->setWednesday($params['wednesday']);
        $consultant->setThursday($params['thursday']);
        $consultant->setFriday($params['friday']);
        $consultant->setSaturday($params['saturday']);
        $consultant->setSunday($params['sunday']);

        $this->em->persist($consultant);
        $this->em->flush();

        return $consultant;
    }

    /**
     * Create a new consultant
     *
     * @param SkedAppCoreBundle:Consultant $consultant
     * @return void
     * @throws \Exception
     */
    public function createNewConsultant($consultant)
    {

        $this->logger->info("Create a new consultant");

        $groupName = "Consultant";

        $groups = $this->em->getRepository("SkedAppCoreBundle:Group")->findByName($groupName);

        if ($groups) {
            $group = $groups[0];
            foreach ($group->getRoles() as $role) {
                $consultant->addConsultantRole($role);
            }
        } else {
            throw new \Exception("Could not find groups matching name:" . $groupName);
        }

        $this->em->persist($consultant);
        $this->em->flush();
        return;
    }

    /**
     * update consultant
     *
     * @param type $consultant
     * @return void
     */
    public function update($consultant)
    {
        $this->logger->info('Save consultant');
        $this->em->persist($consultant);
        $this->em->flush();
        return;
    }

    /**
     * update consultant
     *
     * @param type $consultant
     * @return void
     */
    public function delete($consultant)
    {
        $this->logger->info('delete consultant');

        $consultant->setIsDeleted(true);
        $consultant->setIsActive(false);
        $consultant->setIsLocked(true);

        $this->em->persist($consultant);
        $this->em->flush();
        return;
    }

    /**
     * Get all consultants query
     *
     * @param array $options
     * @return Doctrine Query
     */
    public function listAll($options = array())
    {
        return $this->em
                ->getRepository('SkedAppCoreBundle:Consultant')
                ->getAllActiveConsultantsQuery($options);
    }

    /**
     * Get consultant active bookings
     * 
     * @param integer $consultantId
     * @return array
     */
    public function getConsultantBookings($consultantId)
    {
        $this->logger->info("get all active consultant bookings");

        return $this->em->getRepository("SkedAppCoreBundle:Booking")
                ->getAllConsultantBookings($consultantId);
    }

    /**
     * Get consultants query within a given radius of a given lat and long point
     *
     * @param array $options
     * @return array
     */
    public function listAllWithinRadius($options = array())
    {

        if (!isset($options['radius']))
            $options['radius'] = 5;

        if (!isset($options['lat']))
            $options['lat'] = null;

        if (!isset($options['lng']))
            $options['lng'] = null;

        $results = array(
            'arrResult' => array(),
            'radius' => $options['radius'],
        );

        if ((is_null($options['lat'])) || (is_null($options['lng'])))
            return $results;

        while ((count($results['arrResult']) <= 0) && ($options['radius'] <= 200)) {
            $results['arrResult'] = $this->em
                ->getRepository('SkedAppCoreBundle:Consultant')
                ->getAllActiveConsultantsQueryWithinRadius($options);
            $results['radius'] = $options['radius'];
            $options['radius'] += 5;
        } //while

        return $results;
    }

}
