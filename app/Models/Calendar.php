<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Calendar extends Model
{
    use HasFactory;
    protected $dateFormat = 'Y-m-d';
    protected $table='calendar';
    protected $fillable = ['start_date', 'end_date', 'repeat', 'day', 'start_time', 'end_time', 'client_name'];
}
