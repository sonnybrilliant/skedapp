<?php
namespace SkedApp\BookingBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Doctrine\Common\Persistence\ObjectManager;
use SkedApp\CoreBundle\Entity\Timeslots;

/**
 * SkedApp\BookingBundle\Form\DataTransformer\StringToTimeslotTransformer
 *
 * @author Otto Saayman <otto.saayman@kaizania.co.za>
 * @package SkedAppBookingBundle
 * @subpackage DataTransformerInterface
 * @version 0.0.1
 */
class StringToTimeslotTransformer implements DataTransformerInterface
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
     * Transforms an object (timeslot) to a string (time).
     *
     * @param  Timeslot|null $timeslot
     * @return string
     */
    public function transform($timeslot)
    {
        if (null === $timeslot) {
            return "";
        }

        return $timeslot->getSlot();
    }

    /**
     * Transforms a string (time) to an object (timeslot).
     *
     * @param  string $time
     * @return Timeslot|null
     * @throws TransformationFailedException if object (timeslot) is not found.
     */
    public function reverseTransform($time)
    {
        if (!$time) {
            return null;
        }

        $timeslot = $this->om
            ->getRepository('SkedAppCoreBundle:Timeslots')
            ->findOneBy(array('slot' => $time))
        ;

        if (null === $timeslot) {
            throw new TransformationFailedException(sprintf(
                'A timeslot with time "%s" does not exist!',
                $time
            ));
        }

        return $timeslot;
    }
}