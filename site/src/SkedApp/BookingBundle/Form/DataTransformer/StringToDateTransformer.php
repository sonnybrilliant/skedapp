<?php
namespace SkedApp\BookingBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Doctrine\Common\Persistence\ObjectManager;
use SkedApp\CoreBundle\Entity\Booking;

/**
 * SkedApp\BookingBundle\Form\DataTransformer\StringToDateTransformer
 *
 * @author Otto Saayman <otto.saayman@kaizania.co.za>
 * @package SkedAppBookingBundle
 * @subpackage DataTransformerInterface
 * @version 0.0.1
 */
class StringToDateTransformer implements DataTransformerInterface
{
    /**
     * Transforms an object (date) to a string (date).
     *
     * @param  DateTime|null $date_time
     * @return string
     */
    public function transform($date_time)
    {
        if (null === $date_time) {
            return "";
        }

        if (!is_object($date_time))
          $date_time = new \DateTime($date_time);

        return $date_time->format('Y-m-d');
    }

    /**
     * Transforms a string (date) to an object (DateTime).
     *
     * @param  string $date_time
     * @return DateTime|null
     * @throws TransformationFailedException if object (DateTime) can not be created.
     */
    public function reverseTransform($date_time)
    {
        if (!$date_time) {
            return null;
        }

        $dateTimeObject = new \DateTime($date_time);

        if (null === $dateTimeObject) {
            throw new TransformationFailedException(sprintf(
                'Date is invalid!',
                $date_time
            ));
        }

        return $dateTimeObject;
    }
}