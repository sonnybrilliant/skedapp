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
     * Create default system consultant
     *
     * @param array $params
     * @return saved object
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

       if($groups){
           $group = $groups[0];
           foreach($group->getRoles() as $role){
               $consultant->addConsultantRole($role);
           }
       }else{
           throw new \Exception("Could not find groups matching name:".$groupName);
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
     * @return query
     */
    public function listAll($options = array())
    {
        return $this->em
                ->getRepository('SkedAppCoreBundle:Consultant')
                ->getAllActiveConsultantsQuery($options);
    }


    /**
     * Get consultants query within a given radius of a given lat and long point
     *
     * @param array $options
     * @return query
     */
    public function listAllWithinRadius($arrConf = array ())
    {

        if (!isset ($arrConf['radius']))
          $arrConf['radius'] = 5;

        if (!isset ($arrConf['lat']))
          $arrConf['lat'] = null;

        if (!isset ($arrConf['lng']))
          $arrConf['lng'] = null;

        $arrOut = array(
            'arrResult' => array (),
            'radius' => $arrConf['radius'],
        );

        if ( (is_null($arrConf['lat'])) || (is_null ($arrConf['lng'])) )
          return $arrOut;

        while ( (count($arrOut['arrResult']) <= 0) && ($arrConf['radius'] <= 200) ) {
          $arrOut['arrResult'] = $this->em
                  ->getRepository('SkedAppCoreBundle:Consultant')
                  ->getAllActiveConsultantsQueryWithinRadius($arrConf);
          $arrOut['radius'] = $arrConf['radius'];
          $arrConf['radius'] += 5;
        } //while

        return $arrOut;

    }

}
