<?php

namespace App\Factories;

use App\Enums\RepeatType;
use Carbon\Carbon;

class CalendarEventFactory
{
    public static function createRecurringEvent($event): array
    {
        $startTime = Carbon::parse($event->start_time);
        $endTime = Carbon::parse($event->end_time);

        $diffString = $endTime->diffInHours($startTime);
        $duration = Carbon::createFromTime($diffString)->format('H:i');

        $formatCalendar = [
            'title' => $event->client_name,
            'duration' => $duration,
            'rrule' => [
                'freq' => 'weekly',
                'interval' => 2,
                'byweekday' => [$event->day],
                'dtstart' => $event->start_date . 'T' . $event->start_time,
                'until' => ($event->end_date ? : Carbon::now()->endOfYear()->format('Y-m-d'))
            ]
        ];

        if ($event->repeat === RepeatType::EVERY_WEEK->value) {
            $formatCalendar['rrule']['interval'] = 1;
        }

        $currentWeekNumber = Carbon::parse($event->start_date)->week;
        $currentWeek = Carbon::parse($event->start_date);

        if (($event->repeat === RepeatType::ODD_WEEK->value && $currentWeekNumber % 2 === 0)
            || ($event->repeat === RepeatType::EVEN_WEEK->value && $currentWeekNumber % 2 !== 0)
        ) {
            $formatCalendar['rrule']['dtstart'] = $currentWeek->addWeeks(1)->format('Y-m-d') . 'T' . $event->start_time;
        }

        return $formatCalendar;
    }

    public static function createNonRecurringEvent($event): array
    {
        return [
            'title' => $event->client_name,
            'start' => $event->start_date . 'T' . $event->start_time,
            'end' => ($event->end_date ? : $event->start_date) . 'T' . $event->end_time
        ];
    }
}
