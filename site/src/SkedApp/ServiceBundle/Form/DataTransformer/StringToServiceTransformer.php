<?php
namespace SkedApp\ServiceBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Doctrine\Common\Persistence\ObjectManager;
use SkedApp\CoreBundle\Entity\Service;

/**
 * SkedApp\ServiceBundle\Form\DataTransformer\StringToServiceTransformer
 *
 * @author Otto Saayman <otto.saayman@kaizania.co.za>
 * @package SkedAppServiceBundle
 * @subpackage DataTransformerInterface
 * @version 0.0.1
 */
class StringToServiceTransformer implements DataTransformerInterface
{
    /**
     * @var ObjectManager
     */
    private $om;

    /**
     * @param ObjectManager $om
     */
    public function __construct(ObjectManager $om)
    {
        $this->om = $om;
    }

    /**
     * Transforms an object (service) to a string (id).
     *
     * @param  Service|null $service
     * @return string
     */
    public function transform($service)
    {
        if (null === $service) {
            return "";
        }

        return $service->getId();
    }

    /**
     * Transforms a string (time) to an object (service).
     *
     * @param  string $id
     * @return Service|null
     * @throws TransformationFailedException if object (service) is not found.
     */
    public function reverseTransform($id)
    {
        if (!$id) {
            return null;
        }

        $service = $this->om
            ->getRepository('SkedAppCoreBundle:Service')
            ->find($id)
        ;

        if (null === $service) {
            throw new TransformationFailedException(sprintf(
                'A service with id "%s" does not exist!',
                $id
            ));
        }

        return $service;
    }
}