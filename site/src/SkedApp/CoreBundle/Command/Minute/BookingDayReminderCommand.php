<?php

namespace SkedApp\CoreBundle\Command\Minute;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * SkedApp\CoreBundle\Command\Minute\BookingReminderCommand
 * 
 * Send booking reminder  on the day 07:00 am
 *
 * @author Ronald Conco <ronald.conco@gmail.com>
 * @package SuleCoreBundle
 * @subpackage Form
 * @version 0.0.1
 */
class BookingDayReminderCommand extends ContainerAwareCommand
{

    protected function configure()
    {
        $this
            ->setName('booking:reminder:day')
            ->setDescription('Send booking reminders on the day, 07:00 am')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $logger = $this->getContainer()->get('logger');

        $logger->info("Start day booking reminder command");
        $output->writeln("Start day booking reminder command");

        $this->getContainer()->get("notification.manager")->sendDayBookingReminders();

        $logger->info("End day booking reminder command");
        $output->writeln("Ending day booking reminder command");
    }

}
