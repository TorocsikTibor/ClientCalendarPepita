<?php

namespace App\Http\Controllers;

use App\Enums\Day;
use App\Enums\RepeatType;
use App\Models\Calendar;
use App\Services\DayService;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;

class CalendarController extends Controller
{
    private DayService $dayService;

    public function __construct(DayService $dayService)
    {
        $this->dayService = $dayService;
    }

    public function index()
    {
        return view('calendar');
    }

    public function fetchCalendar()
    {
        $calendar = Calendar::all();
        $newCalendar = [];

        foreach ($calendar as $item) {
            $timeStart = Carbon::parse($item->start_time);
            $timeEnd = Carbon::parse($item->end_time);
            $diffString = $timeEnd->diffInHours($timeStart);
            $newCarbon = Carbon::createFromTime($diffString)->format('H:i');

            if ($item->repeat === RepeatType::NONE->value) {
                $formatCalendar = [
                    'title' => $item->client_name,
                    'start' => $item->start_date . 'T' . $item->start_time,
                    'end' => ($item->end_date ? $item->end_date : $item->start_date) . 'T' . $item->end_time
                ];
            } else {
                $formatCalendar = [
                    'title' => $item->client_name,
                    'duration' => $newCarbon,
                    'rrule' => [
                        'freq' => 'weekly',
                        'interval' => 2,
                        'byweekday' => [$item->day],
                        'dtstart' => $item->start_date . 'T' . $item->start_time,
                        'until' => ($item->end_date ? $item->end_date : Carbon::now()->endOfYear()->format('Y-m-d'))
                    ]
                ];
            }

            if ($item->repeat === RepeatType::EVERY_WEEK->value) {
                $formatCalendar['rrule']['interval'] = 1;
            }

            if ($item->repeat === RepeatType::ODD_WEEK->value) {
                $currentWeekNumber = Carbon::parse($item->start_date)->week;
                $currentWeek = Carbon::parse($item->start_date);
                if ($currentWeekNumber % 2 === 0) {
                    $formatCalendar['rrule']['dtstart'] = $currentWeek->addWeeks(1)->format('Y-m-d') . 'T' . $item->start_time;
                }
            }

            if ($item->repeat === RepeatType::EVEN_WEEK->value) {
                $currentWeekNumber = Carbon::parse($item->start_date)->week;
                $currentWeek = Carbon::parse($item->start_date);
                if ($currentWeekNumber % 2 !== 0) {
                    $formatCalendar['rrule']['dtstart'] = $currentWeek->addWeeks(1)->format('Y-m-d') . 'T' . $item->start_time;
                }
            }

            $newCalendar[] = $formatCalendar;
        }

        return response()->json($newCalendar);
    }

    public function create(Request $request)
    {
        $calendar = Calendar::all();
        $newEventStart = Carbon::parse($request->get('startTime'))->format('Y-m-d H:i:s');
        $newEventEnd = Carbon::parse($request->get('endTime'))->format('Y-m-d H:i:s');
        $newEventStart1 = Carbon::create($newEventStart);
        $newEventEnd1 = Carbon::create($newEventEnd);


        foreach ($calendar as $item) {
            $eventStart = Carbon::parse($item->start_date . ' ' . $item->start_time);
            $eventEnd = Carbon::parse($item->end_date . ' ' . $item->end_time);
            $dayName = $this->dayService->getNameFromNumber($item->day);

            $start = Carbon::parse($request->get('startTime'))->format('Y-m-d');
            $end = Carbon::parse($request->get('endTime'))->format('Y-m-d');
            $falseDateStart = Carbon::create($start . ' ' . $item->start_time);
            $falseDateEnd = Carbon::create($end . ' ' . $item->end_time);

            if ($item->repeat === RepeatType::NONE->value) {
                $eventEnd = $eventEnd ?: $item->start_date . ' ' . $item->end_time;
                if ($eventStart->between($newEventStart1, $newEventEnd1) || $eventEnd->between($newEventStart1, $newEventEnd1)) {
                    return response()->json(['error' => 'Overlap between dates']);
                }
            }

            if ($item->repeat === RepeatType::EVERY_WEEK->value) {
                if (Carbon::parse($newEventStart1)->dayName === $dayName) {
                    if ($falseDateStart->between($newEventStart1, $newEventEnd1) || $falseDateEnd->between($newEventStart1, $newEventEnd1)) {
                        return response()->json(['error' => 'Overlap between dates']);
                    }
                }
            }

            $currentWeekNumber = Carbon::parse($newEventStart)->week;

            $newEventStart = Carbon::parse($request->get('startTime'));

            if ($item->repeat === RepeatType::ODD_WEEK->value) {
                if ($currentWeekNumber % 2 !== 0) {
                    if (Carbon::parse($newEventStart)->dayName === $dayName) {
                        if ($falseDateStart->between($newEventStart1, $newEventEnd1) || $falseDateEnd->between($newEventStart1, $newEventEnd1)) {
                            return response()->json(['error' => 'Overlap between dates'], 201);
                        }
                    }
                }
            }
            if ($item->repeat === RepeatType::EVEN_WEEK->value) {
                if ($currentWeekNumber % 2 === 0) {
                    if (Carbon::parse($newEventStart)->dayName === $dayName) {
                        if ($falseDateStart->between($newEventStart1, $newEventEnd1) || $falseDateEnd->between($newEventStart1, $newEventEnd1)) {
                            return response()->json(['error' => 'Overlap between dates'], 201);
                        }
                    }
                }
            }
        }

        $newCalendar = Calendar::create([
            'start_date' => Carbon::parse($request->get('startTime'))->format('Y-m-d'),
            'end_date' => Carbon::parse($request->get('endTime'))->format('Y-m-d'),
            'repeat' => 0,
            'day' => $this->dayService->getNameFromString(Carbon::parse($request->get('startTime'))->dayName),
            'start_time' => Carbon::parse($request->get('startTime'))->format('H:i:s'),
            'end_time' => Carbon::parse($request->get('endTime'))->format('H:i:s'),
            'client_name' => $request->get('clientName'),
        ]);

        return response()->json($newCalendar);
    }
}
