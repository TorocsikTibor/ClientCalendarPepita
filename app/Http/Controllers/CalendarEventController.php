<?php

namespace App\Http\Controllers;

use App\Services\CalendarEventService;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CalendarEventController extends Controller
{
    private CalendarEventService $calendarEventService;

    public function __construct(CalendarEventService $calendarService)
    {
        $this->calendarEventService = $calendarService;
    }

    public function index(): Factory|Application|View
    {
        return view('calendar.show');
    }

    public function fetchCalendar(): JsonResponse
    {
        return response()->json($this->calendarEventService->fetchCalendar());
    }

    public function create(Request $request): JsonResponse
    {
        $startDateTime = $request->get('startTime');
        $endDateTime = $request->get('endTime');
        $clientName = $request->get('clientName');

        try {
            if (!$this->calendarEventService->checkCalendar($startDateTime, $endDateTime)) {
                return response()->json(['error' => 'Overlap between events'], 400);
            }

            return response()->json($this->calendarEventService->create($startDateTime, $endDateTime, $clientName));
        } catch (\Exception $exception) {
            return response()->json(['error' => $exception->getMessage()], 500);
        }

    }
}
