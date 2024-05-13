<?php

namespace App\Services;

class DayService
{
    public function getNameFromNumber($dayNumber): string
    {
        return match ($dayNumber) {
            0 => 'Monday',
            1 => 'Tuesday',
            2 => 'Wednesday',
            3 => 'Thursday',
            4 => 'Friday',
            5 => 'Saturday',
            6 => 'Sunday',
            default => throw new \InvalidArgumentException('Invalid day number'),
        };
    }

    public function getNameFromString($dayName): int|string
    {
        return match ($dayName) {
            'Monday' => 0,
            'Tuesday' => 1,
            'Wednesday' => 2,
            'Thursday' => 3,
            'Friday' => 4,
            'Saturday' => 5,
            'Sunday' => 6,
            default => throw new \InvalidArgumentException('Invalid day name'),
        };
    }
}
