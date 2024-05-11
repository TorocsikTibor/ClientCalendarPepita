<?php

namespace App\Services;

class DayService
{
    public function getNameFromNumber($number)
    {
        switch ($number) {
            case 0:
                return 'Monday';
            case 1:
                return 'Tuesday';
            case 2:
                return 'Wednesday';
            case 3:
                return 'Thursday';
            case 4:
                return 'Friday';
            case 5:
                return 'Saturday';
            case 6:
                return 'Sunday';
            default:
                return 'Invalid day';
        }
    }

    public function getNameFromString($string)
    {
        switch ($string) {
            case 'Monday':
                return 0;
            case 'Tuesday':
                return 1;
            case 'Wednesday':
                return 2;
            case 'Thursday':
                return 3;
            case 'Friday':
                return 4;
            case 'Saturday':
                return 5;
            case 'Sunday':
                return 6;
            default:
                return 'Invalid day';
        }
    }
}
