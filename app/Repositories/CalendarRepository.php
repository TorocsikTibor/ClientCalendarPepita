<?php

namespace App\Repositories;

use App\DTO\CalendarDTO;
use App\Models\Calendar;
use Illuminate\Database\Eloquent\Collection;

class CalendarRepository
{
    public function getCalendar(): Collection
    {
        return Calendar::all();
    }

    public function create(CalendarDTO $calendarDTO): mixed
    {
        return Calendar::create([
            'start_date' => $calendarDTO->start_date,
            'end_date' => $calendarDTO->end_date,
            'repeat' => $calendarDTO->repeat,
            'day' => $calendarDTO->day,
            'start_time' => $calendarDTO->start_time,
            'end_time' => $calendarDTO->end_time,
            'client_name' => $calendarDTO->client_name,
        ]);
    }
}
