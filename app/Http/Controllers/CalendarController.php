<?php

namespace App\Http\Controllers;

use App\DTO\CalendarDTO;
use App\Services\CalendarService;
use App\Services\DayService;
use Carbon\Carbon;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CalendarController extends Controller
{
    private CalendarService $calendarService;
    private DayService $dayService;

    public function __construct(CalendarService $calendarService, DayService $dayService)
    {
        $this->calendarService = $calendarService;
        $this->dayService = $dayService;
    }

    public function index(): Factory|Application|View
    {
        return view('calendar.show');
    }

    public function fetchCalendar(): JsonResponse
    {
        return response()->json($this->calendarService->fetchCalendar());
    }

    public function create(Request $request): JsonResponse
    {
        $startDateTime = $request->get('startTime');
        $endDateTime = $request->get('endTime');
        $clientName = $request->get('clientName');

        if (!$this->calendarService->checkCalendar($startDateTime, $endDateTime)) {
            return response()->json(['error' => 'Overlap between events'], 402);
        }

        return response()->json($this->calendarService->create($startDateTime, $endDateTime, $clientName));
    }
}
