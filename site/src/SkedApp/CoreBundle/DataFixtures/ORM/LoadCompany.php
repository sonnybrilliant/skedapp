<?php

namespace SkedApp\CoreBundle\DataFixtures\ORM ;

use SkedApp\CoreBundle\Entity\Company ;
use Doctrine\Common\Persistence\ObjectManager ;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface ;
use Doctrine\Common\DataFixtures\AbstractFixture ;

/**
 * Load default system default company
 *
 * @author Ronald Conco <ronald.conco@gmail.com>
 * @package SkedAppCoreBundle
 * @subpackage DataFixtures
 * @version 0.0.1
 */
class LoadCompany extends AbstractFixture implements OrderedFixtureInterface
{

    public function load( ObjectManager $manager )
    {
        $company = new Company() ;
        $company->setName('Skedapp');
        $company->setDescription('this is a description');
        $manager->persist($company) ;
        
        $company_hair = new Company() ;
        $company_hair->setName("Sonny's hair Palace");
        $company_hair->setDescription('this is a description');
        $manager->persist($company_hair) ;

        $manager->flush() ;

        $this->addReference('company-default' , $company) ;         
    }

    public function getOrder()
    {
        return 1 ;
    }

}
