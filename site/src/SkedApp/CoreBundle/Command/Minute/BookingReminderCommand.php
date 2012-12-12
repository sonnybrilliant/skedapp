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
 * Send booking reminder a day before the booking
 *
 * @author Ronald Conco <ronald.conco@gmail.com>
 * @package SuleCoreBundle
 * @subpackage Form
 * @version 0.0.1
 */
class BookingReminderCommand extends ContainerAwareCommand
{

    protected function configure()
    {
        $this
            ->setName('booking:reminder')
            ->setDescription('Send booking reminders')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $logger = $this->getContainer()->get('logger');

        $logger->info("Start booking reminder command");
        $output->writeln("Start booking reminder command");

        $this->getContainer()->get("notification.manager")->sendBookingReminders();

        $logger->info("End booking reminder command");
        $output->writeln("Ending booking reminder command");
    }

}
