<?php

namespace SkedApp\CoreBundle\DataFixtures\ORM;

use SkedApp\CoreBundle\Entity\Service;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\DataFixtures\AbstractFixture;

/**
 * Load default system user services
 *
 * @author Ronald Conco <ronald.conco@gmail.com>
 * @version 1.0
 * @package SkedAppCoreBundle
 * @subpackage DataFixtures
 * @version 0.0.1
 */
class LoadService extends AbstractFixture implements OrderedFixtureInterface {

    public function load(ObjectManager $manager) {
        $service1 = new Service('Blow wave');
        $service1->setCategory($this->getReference('category-beauty'));
        $manager->persist($service1);

        $service2 = new Service('Womans Cut');
        $service2->setCategory($this->getReference('category-beauty'));
        $manager->persist($service2);

        $service3 = new Service('Mens Cut');
        $service3->setCategory($this->getReference('category-beauty'));
        $manager->persist($service3);

        $service4 = new Service('Perm');
        $service4->setCategory($this->getReference('category-beauty'));
        $manager->persist($service4);        

        $manager->flush();

    }

    public function getOrder() {
        return 2;
    }

}
