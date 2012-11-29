<?php

namespace SkedApp\CoreBundle\DataFixtures\ORM ;

use SkedApp\CoreBundle\Entity\Status ;
use Doctrine\Common\Persistence\ObjectManager ;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface ;
use Doctrine\Common\DataFixtures\AbstractFixture ;

/**
 * Load default system status
 *
 * @author Ronald Conco <ronald.conco@gmail.com>
 * @version 1.0
 * @package SkedAppCoreBundle
 * @subpackage DataFixtures
 * @version 0.0.1
 */
class LoadStatus extends AbstractFixture implements OrderedFixtureInterface
{

    public function load( ObjectManager $manager )
    {

        //create statuses
        $active = new Status() ;
        $active->setName('Active') ;
        $active->setCode(10) ;
        $manager->persist($active) ;

        $inactive = new Status() ;
        $inactive->setName('Inactive') ;
        $inactive->setCode(20) ;
        $manager->persist($inactive) ;

        $new = new Status() ;
        $new->setName('New') ;
        $new->setCode(30) ;
        $manager->persist($new) ;

        $old = new Status() ;
        $old->setName('Old') ;
        $old->setCode(40) ;
        $manager->persist($old) ;

        $completed = new Status() ;
        $completed->setName('Completed') ;
        $completed->setCode(50) ;
        $manager->persist($completed) ;

        $cancelled = new Status() ;
        $cancelled->setName('Cancelled') ;
        $cancelled->setCode(60) ;
        $manager->persist($cancelled) ;

        $progress = new Status() ;
        $progress->setName('In Progress') ;
        $progress->setCode(70) ;
        $manager->persist($progress) ;

        $pending = new Status() ;
        $pending->setName('Pending') ;
        $pending->setCode(80) ;
        $manager->persist($pending) ;
        
        $pendingEncoding = new Status() ;
        $pendingEncoding->setName('Pending encoding') ;
        $pendingEncoding->setCode(90) ;
        $manager->persist($pendingEncoding) ;        

        $blocked = new Status() ;
        $blocked->setName('Blocked') ;
        $blocked->setCode(100) ;
        $manager->persist($blocked) ;

        $error = new Status() ;
        $error->setName('Error') ;
        $error->setCode(110) ;
        $manager->persist($error) ;

        $failed = new Status() ;
        $failed->setName('Failed') ;
        $failed->setCode(120) ;
        $manager->persist($failed) ;

        $objSuccess = new Status() ;
        $objSuccess->setName('Successful') ;
        $objSuccess->setCode(130) ;
        $manager->persist($objSuccess) ;

        $timeout = new Status() ;
        $timeout->setName('Timed Out') ;
        $timeout->setCode(140) ;
        $manager->persist($timeout) ;

        $locked = new Status() ;
        $locked->setName('Locked') ;
        $locked->setCode(150) ;
        $manager->persist($locked) ;

        $inviteSent = new Status() ;
        $inviteSent->setName('Invite Sent') ;
        $inviteSent->setCode(160) ;
        $manager->persist($inviteSent) ;
        
        $booked = new Status() ;
        $booked->setName('Booked') ;
        $booked->setCode(170) ;
        $manager->persist($booked) ;  
    
        $confirm = new Status() ;
        $confirm->setName('Confirmed') ;
        $confirm->setCode(180) ;
        $manager->persist($confirm) ;
        
        $manager->flush() ;

        $this->addReference('status-active' , $active) ;
    }

    public function getOrder()
    {
        return 1 ;
    }

}
