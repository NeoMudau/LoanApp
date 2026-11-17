<?php

namespace App\Scheduler;

use Symfony\Component\Scheduler\Attribute\AsSchedule;
use Symfony\Component\Scheduler\RecurringMessage;
use Symfony\Component\Scheduler\Schedule;
use Symfony\Component\Scheduler\ScheduleProviderInterface;
use App\Scheduler\Message\RunLoanRollover;

#[AsSchedule('loan_rollover')]
class LoanRolloverSchedule implements ScheduleProviderInterface
{
    public function getSchedule(): Schedule
    {
        return (new Schedule())->add(
            // Runs at midnight on the 1st of every month
            //RecurringMessage::cron('0 0 1 * *', new RunLoanRollover())
            RecurringMessage::cron('* * * * *', new RunLoanRollover())
        );
    }
}