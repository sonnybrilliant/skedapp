<?php

namespace SkedApp\CoreBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * consultantRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class ConsultantRepository extends EntityRepository
{

    /**
     * Get all active consultants query
     *
     * @author Ronald Conco <ronald.conco@kaizania.com>
     * @return Resultset
     */
    public function getAllActiveConsultantsQuery($options)
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

    /**
     * Get all active consultants by Company query
     *
     * @author Otto Saayman <otto.saayman@creativecloud.co.za>
     * @return Resultset
     */
    public function getAllActiveConsultantsByCompanyQuery(\SkedApp\CoreBundle\Entity\Company $company, $options)
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
        $qb->where('c.isDeleted =  :status')
                ->andWhere('c.company =  :company')
                ->setParameters(array('status' => false, 'company' => $company));
        $qb->orderBy($options['sort'], $options['direction']);
        return $qb->getQuery()->execute();
    }

    /**
     * Get all active consultants query within a radius based on a lat/ long point and radius
     *
     * @author Otto Saayman <otto.saayman@kaizania.co.za>
     * @return Resultset
     */
    public function getAllActiveConsultantsQueryWithinRadius($options)
    {

        $defaultOptions = array(
            'sort' => 'c.id',
            'direction' => 'asc',
            'consultantServices' => null
        );

        foreach ($options as $key => $values) {
            if (!$values)
                $options[$key] = $defaultOptions[$key];
        }

        if (!isset ($options['categoryId']))
            $options['categoryId'] = 0;

        if (!isset ($options['consultantServices']))
            $options['consultantServices'] = array();

        $config = $this->getEntityManager()->getConfiguration();
        $config->addCustomNumericFunction('ACOS', 'DoctrineExtensions\Query\Mysql\Acos');
        $config->addCustomNumericFunction('COS', 'DoctrineExtensions\Query\Mysql\Cos');
        $config->addCustomNumericFunction('RADIANS', 'DoctrineExtensions\Query\Mysql\Radians');
        $config->addCustomNumericFunction('SIN', 'DoctrineExtensions\Query\Mysql\Sin');

        $qb = $this->createQueryBuilder('c');
        $qb->select('c')
                ->innerJoin ('c.company', 'comp')
                ->innerJoin ('c.consultantServices', 's');
        $qb->where('c.isDeleted =  :status')
                ->andWhere('( 6371 * ACOS( COS( RADIANS(:latitude) ) * COS( RADIANS( comp.lat ) ) * COS( RADIANS( comp.lng ) - RADIANS(:longitude) ) '
                    . ' + SIN( RADIANS(:latitude) ) * SIN( RADIANS( comp.lat ) ) ) ) <= :radius')
                ->setParameters(array ('status' => false, 'latitude' => $options['lat'], 'longitude' => $options['lng'], 'radius' => $options['radius']));

        if ($options['categoryId'] > 0) {
            $qb->andWhere('s.category = :category')
                    ->setParameter('category', $options['categoryId']);
        }

        if ( (count($options['consultantServices']) > 0) && ($options['consultantServices'][0] > 0) ) {
            $qb->andWhere('s.id IN (:consultants)')
                    ->setParameter('consultants', $options['consultantServices']);
        }

        $qb->add('orderBy', $options['sort'] . ' ' . $options['direction'], true);


        $arrOut = $qb->getQuery()->execute();

        //Order consultants from nearest to furthest from location
        for ($intCnt1 = 0; $intCnt1 < (count ($arrOut) - 1); $intCnt1++) {
          for ($intCnt2 = 1; $intCnt2 < count ($arrOut); $intCnt2++) {
            if ($arrOut[$intCnt1]->getDistanceFromPosition($options['lat'], $options['lng']) > $arrOut[$intCnt2]->getDistanceFromPosition($options['lat'], $options['lng'])) {
              $objDummy = $arrOut[$intCnt2];
              $arrOut[$intCnt2] = $arrOut[$intCnt1];
              $arrOut[$intCnt1] = $objDummy;
            }
          }
        }

        return $arrOut;

    }

    /**
     * Get consultant by service
     *
     * @param integer $serviceId
     * @return type
     */
    public function getByService($serviceId)
    {
        $qb = $this->createQueryBuilder('c')
            ->innerJoin('c.consultantServices', 's')
            ->where('s.id = :serviceId')
            ->setParameter('serviceId', $serviceId);

        return $qb->getQuery()->execute();
    }

    /**
     * Get all active consultants query
     *
     * @author Ronald Conco <ronald.conco@kaizania.com>
     * @return Resultset
     */
    public function getAllActiveQuery()
    {
        $qb = $this->createQueryBuilder('c')->select('c');
        $qb->where('c.isDeleted =  :status')->setParameter('status', false);
        return $qb->getQuery()->execute();
    }

    /**
     * Get all active consultants by Company query
     *
     * @author Otto Saayman <otto.saayman@creativecloud.co.za>
     * @return Resultset
     */
    public function getAllActiveByCompanyQuery(\SkedApp\CoreBundle\Entity\Company $company)
    {
        $qb = $this->createQueryBuilder('c')->select('c');
        $qb->where('c.isDeleted =  :status')
                ->andWhere('c.company =  :company')
                ->setParameters(array('status' => false, 'company' => $company));
        return $qb->getQuery()->execute();
    }

}
