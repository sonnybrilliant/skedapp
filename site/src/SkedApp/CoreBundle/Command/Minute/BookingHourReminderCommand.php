<?php

namespace SkedApp\CoreBundle\Command\Minute;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * SkedApp\CoreBundle\Command\Minute\BookingHourReminderCommand
 * 
 * Send booking reminder  on the day 07:00 am
 *
 * @author Ronald Conco <ronald.conco@gmail.com>
 * @package SuleCoreBundle
 * @subpackage Form
 * @version 0.0.1
 */
class BookingHourReminderCommand extends ContainerAwareCommand
{

    protected function configure()
    {
        $this
            ->setName('booking:reminder:hour')
            ->setDescription('Send booking reminders on the hour')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $logger = $this->getContainer()->get('logger');

        $logger->info("Start hour booking reminder command");
        $output->writeln("Start hour booking reminder command");

        $this->getContainer()->get("notification.manager")->sendHourBookingReminders();

        $logger->info("End hour booking reminder command");
        $output->writeln("Ending hour booking reminder command");
    }

}
