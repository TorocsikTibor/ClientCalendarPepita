<?php

namespace App\Repositories;

use App\DTO\CalendarEventDTO;
use App\Models\CalendarEvent;
use Illuminate\Database\Eloquent\Collection;

class CalendarEventRepository
{
    public function getCalendar(): Collection
    {
        return CalendarEvent::all();
    }

    public function create(CalendarEventDTO $calendarDTO): mixed
    {
        return CalendarEvent::create([
            'start_date' => $calendarDTO->getStartDate(),
            'end_date' => $calendarDTO->getEndDate(),
            'repeat' => $calendarDTO->getRepeat(),
            'day' => $calendarDTO->getDay(),
            'start_time' => $calendarDTO->getStartTime(),
            'end_time' => $calendarDTO->getEndTime(),
            'client_name' => $calendarDTO->getClientName(),
        ]);
    }
}
