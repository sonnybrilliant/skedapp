<?php

namespace SkedApp\CoreBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * DaysOfTheWeekRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class DaysOfTheWeekRepository extends EntityRepository
{

    /**
     * Get day of the week
     *
     * @param string $name
     * @return type
     */
    public function getName($name)
    {
        $objQueuryBuilder = $this->createQueryBuilder('s')
            ->where('s.name = :name')
            ->setParameter('name', $name);

        return $objQueuryBuilder->getQuery()->getSingleResult();
    }

}
