<?php

namespace App\Http\Controllers;

use App\Services\CalendarService;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CalendarController extends Controller
{
    private CalendarService $calendarService;

    public function __construct(CalendarService $calendarService)
    {
        $this->calendarService = $calendarService;
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
