<?php

use App\Http\Controllers\CalendarController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', [CalendarController::class, 'index'])->name('calendar.index');
Route::get('/calendar/fetch', [CalendarController::class, 'fetchCalendar'])->name('calendar.fetchCalendar');
Route::post('/calendar/create', [CalendarController::class, 'create'])->name('calendar.create');
