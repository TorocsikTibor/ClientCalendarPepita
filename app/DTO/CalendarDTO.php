<?php

namespace App\DTO;

use JsonSerializable;

class CalendarDTO implements JsonSerializable
{
    public string $start_date;
    public string $end_date;
    public int $repeat;
    public int $day;
    public string $start_time;
    public string $end_time;
    public string $client_name;

    public function __construct($start_date, $end_date, $repeat, $day, $start_time, $end_time, $client_name)
    {
        $this->start_date = $start_date;
        $this->end_date = $end_date;
        $this->repeat = $repeat;
        $this->day = $day;
        $this->start_time = $start_time;
        $this->end_time = $end_time;
        $this->client_name = $client_name;
    }

    public function jsonSerialize(): mixed
    {
        return [
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'repeat' => $this->repeat,
            'day' => $this->day,
            'start_time' => $this->start_time,
            'end_time' => $this->end_time,
            'client_name' => $this->client_name,
        ];
    }
}
