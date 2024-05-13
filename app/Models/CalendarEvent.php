<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CalendarEvent extends Model
{
    use HasFactory;

    protected $dateFormat = 'Y-m-d';
    protected $table = 'calendar_event';
    protected $fillable = ['start_date', 'end_date', 'repeat', 'day', 'start_time', 'end_time', 'client_name'];
}
