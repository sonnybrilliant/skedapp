<?php
namespace SkedApp\ConsultantBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Doctrine\Common\Persistence\ObjectManager;
use SkedApp\CoreBundle\Entity\Consultant;

/**
 * SkedApp\ConsultantBundle\Form\DataTransformer\StringToConsultantTransformer
 *
 * @author Otto Saayman <otto.saayman@kaizania.co.za>
 * @package SkedAppConsultantBundle
 * @subpackage DataTransformerInterface
 * @version 0.0.1
 */
class StringToConsultantTransformer implements DataTransformerInterface
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
     * Transforms an object (consultant) to a string (id).
     *
     * @param  Consultant|null $consultant
     * @return string
     */
    public function transform($consultant)
    {
        if (null === $consultant) {
            return "";
        }

        return $consultant->getId();
    }

    /**
     * Transforms a string (time) to an object (consultant).
     *
     * @param  string $id
     * @return Consultant|null
     * @throws TransformationFailedException if object (consultant) is not found.
     */
    public function reverseTransform($id)
    {
        if (!$id) {
            return null;
        }

        $consultant = $this->om
            ->getRepository('SkedAppCoreBundle:Consultant')
            ->find($id)
        ;

        if (null === $consultant) {
            throw new TransformationFailedException(sprintf(
                'A consultant with id "%s" does not exist!',
                $id
            ));
        }

        return $consultant;
    }
}