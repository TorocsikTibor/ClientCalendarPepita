<?php

namespace App\Factories;

use App\DTO\NonRecuringEventDTO;
use App\DTO\RecuringEventDTO;
use App\Enums\RepeatType;
use App\Models\CalendarEvent;
use Carbon\Carbon;

class CalendarEventFactory
{
    public function create(CalendarEvent $event): RecuringEventDTO|NonRecuringEventDTO
    {
        if ($event->repeat === RepeatType::NONE->value) {
            return $this->createNonRecurringEvent($event);
        }
        return $this->createRecurringEvent($event);
    }

    private function createRecurringEvent(CalendarEvent $event): RecuringEventDTO
    {
        $startTime = Carbon::parse($event->start_time);
        $endTime = Carbon::parse($event->end_time);

        $diffString = $endTime->diffInHours($startTime);
        $duration = Carbon::createFromTime($diffString)->format('H:i');


        $formatCalendar = new RecuringEventDTO(
            $event->client_name, $duration,
            'weekly',
            2,
            [$event->day],
            $event->start_date . 'T' . $event->start_time,
            $event->end_date ?: Carbon::now()->endOfYear()->format('Y-m-d')
        );

        if ($event->repeat === RepeatType::EVERY_WEEK->value) {
            $formatCalendar->setRruleByKey('freq', 1);
        }

        $currentWeekNumber = Carbon::parse($event->start_date)->week;
        $currentWeek = Carbon::parse($event->start_date);

        if (($event->repeat === RepeatType::ODD_WEEK->value && $currentWeekNumber % 2 === 0)
            || ($event->repeat === RepeatType::EVEN_WEEK->value && $currentWeekNumber % 2 !== 0)
        ) {
            $formatCalendar->setRruleByKey('dtstart', $currentWeek->addWeeks(1)->format('Y-m-d') . 'T' . $event->start_time);
        }

        return $formatCalendar;
    }

    private function createNonRecurringEvent(CalendarEvent $event): NonRecuringEventDTO
    {
        return new NonRecuringEventDTO(
            $event->client_name,
            $event->start_date . 'T' . $event->start_time,
            ($event->end_date ?: $event->start_date) . 'T' . $event->end_time
        );
    }
}
