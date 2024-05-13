<?php

namespace App\Services;

class DayService
{
    public function getNameFromNumber($number): string
    {
        return match ($number) {
            0 => 'Monday',
            1 => 'Tuesday',
            2 => 'Wednesday',
            3 => 'Thursday',
            4 => 'Friday',
            5 => 'Saturday',
            6 => 'Sunday',
            default => 'Invalid day',
        };
    }

    public function getNameFromString($string): int|string
    {
        return match ($string) {
            'Monday' => 0,
            'Tuesday' => 1,
            'Wednesday' => 2,
            'Thursday' => 3,
            'Friday' => 4,
            'Saturday' => 5,
            'Sunday' => 6,
            default => 'Invalid day',
        };
    }
}
