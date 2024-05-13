<?php

namespace App\DTO;

use JsonSerializable;

class RecuringEventDTO implements JsonSerializable
{
    private string $title;
    private string $duration;
    private array $rrule;

    public function __construct(
        string $title,
        string $duration,
        string $freq,
        int $interval,
        array $weekDays,
        string $dateStart,
        string $until
    ) {
        $this->title = $title;
        $this->duration = $duration;
        $this->rrule = ['freq' => $freq, 'interval' => $interval, 'byweekday' => $weekDays, 'dtstart' => $dateStart, 'until' => $until];
    }
    public function getTitle(): string
    {
        return $this->title;
    }

    public function getDuration(): string
    {
        return $this->duration;
    }

    public function getRrule(): array
    {
        return $this->rrule;
    }

    public function setRruleByKey(string $key, mixed $value): void
    {
        $this->rrule[$key] = $value;
    }

    public function jsonSerialize(): array
    {
        return [
            'title' => $this->title,
            'duration' => $this->duration,
            'rrule' => $this->rrule,
        ];
    }
}
