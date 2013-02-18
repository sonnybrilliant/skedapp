<?php

namespace SkedApp\CoreBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * CustomerPotentialRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class CustomerPotentialRepository extends EntityRepository
{

    /**
     * Get all active customers potential query
     *
     * @author Otto Saayman <otto.saayman@creativecloud.co.za>
     * @return Resultset
     */
    public function getAllActiveCustomersPotentialQuery($options)
    {

        $defaultOptions = array(
            'sort' => 'c.id',
            'direction' => 'asc'
        );

        foreach ($options as $key => $values) {
            if (!$values)
                $options[$key] = $defaultOptions[$key];
        }

        $qb = $this->createQueryBuilder('c')->select('c');
        $qb->where('c.isDeleted =  :status')->setParameter('status', false);
        $qb->orderBy($options['sort'], $options['direction']);
        return $qb->getQuery()->execute();
    }

}
