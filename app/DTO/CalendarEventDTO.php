<?php

namespace App\DTO;

use JsonSerializable;

class CalendarEventDTO implements JsonSerializable
{
    private string $startDate;
    private string $endDate;
    private int $repeat;
    private int $day;
    private string $startTime;
    private string $endTime;
    private string $clientName;

    public function __construct(
        string $startDate,
        string $endDate,
        int    $repeat,
        int    $day,
        string $startTime,
        string $endTime,
        string $clientName
    ) {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->repeat = $repeat;
        $this->day = $day;
        $this->startTime = $startTime;
        $this->endTime = $endTime;
        $this->clientName = $clientName;
    }

    public function getStartDate(): string
    {
        return $this->startDate;
    }

    public function getEndDate(): string
    {
        return $this->endDate;
    }

    public function getRepeat(): int
    {
        return $this->repeat;
    }

    public function getDay(): int
    {
        return $this->day;
    }

    public function getStartTime(): string
    {
        return $this->startTime;
    }

    public function getEndTime(): string
    {
        return $this->endTime;
    }

    public function getClientName(): string
    {
        return $this->clientName;
    }

    public function jsonSerialize(): array
    {
        return [
            'start_date' => $this->startDate,
            'end_date' => $this->endDate,
            'repeat' => $this->repeat,
            'day' => $this->day,
            'start_time' => $this->startTime,
            'end_time' => $this->endTime,
            'client_name' => $this->clientName,
        ];
    }
}
