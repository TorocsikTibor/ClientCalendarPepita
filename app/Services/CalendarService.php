<?php

namespace App\Services;

use App\DTO\CalendarDTO;
use App\Enums\RepeatType;
use App\Factories\CalendarEventFactory;
use App\Repositories\CalendarRepository;
use Carbon\Carbon;

class CalendarService
{
    private CalendarRepository $calendarRepository;
    private DayService $dayService;

    public function __construct(CalendarRepository $calendarRepository, DayService $dayService)
    {
        $this->calendarRepository = $calendarRepository;
        $this->dayService = $dayService;
    }
    public function fetchCalendar(): array
    {
        $calendarEvents = $this->calendarRepository->getCalendar();
        $calendarEventDetails = [];

        foreach ($calendarEvents as $event) {
            if ($event->repeat === RepeatType::NONE->value) {
                $calendarEventDetails[] = CalendarEventFactory::createNonRecurringEvent($event);
            } else {
                $calendarEventDetails[] = CalendarEventFactory::createRecurringEvent($event);
            }
        }

        return $calendarEventDetails;
    }

    public function checkCalendar(string $startDateTime, string $endDateTime): bool
    {
        $calendarEvents = $this->calendarRepository->getCalendar();

        $formatedStartDateTime = Carbon::create($startDateTime)->format('Y-m-d H:i:s');
        $formatedEndDateTime = Carbon::create($endDateTime)->format('Y-m-d H:i:s');

        $newEventStart = Carbon::create($formatedStartDateTime);
        $newEventEnd = Carbon::create($formatedEndDateTime);

        foreach ($calendarEvents as $event) {
            $startEventDateTime = Carbon::parse($event->start_date . ' ' . $event->start_time);
            $endEventDateTime = Carbon::parse(($event->end_date ? : $event->start_date) . ' ' . $event->end_time);

            $dayName = $this->dayService->getNameFromNumber($event->day); //exception kezelés

            $futureDateStart = Carbon::create($newEventStart->format('Y-m-d') . ' ' . $event->start_time);
            $futureDateEnd = Carbon::create($newEventEnd->format('Y-m-d') . ' ' . $event->end_time);

            $collision = match ($event->repeat) {
                RepeatType::NONE->value => $this->checkCollisionForNone($startEventDateTime, $endEventDateTime, $newEventStart, $newEventEnd),
                RepeatType::EVERY_WEEK->value => $this->checkCollisionForEveryWeek($newEventStart, $newEventEnd,$futureDateStart, $futureDateEnd, $dayName),
                RepeatType::ODD_WEEK->value => $this->checkCollisionForOddWeek($newEventStart, $newEventEnd, $futureDateStart, $futureDateEnd, $dayName),
                RepeatType::EVEN_WEEK->value => $this->checkCollisionForEvenWeek($newEventStart, $newEventEnd, $futureDateStart, $futureDateEnd, $dayName),
            };

            if (!$collision) {
                return false;
            }
        }
        return true;
    }

    public function create($startDateTime, $endDateTime, $clientName)
    {
        $calendarDTO = new CalendarDTO(
            Carbon::parse($startDateTime)->format('Y-m-d'),
            Carbon::parse($endDateTime)->format('Y-m-d'),
            0,
            $this->dayService->getNameFromString(Carbon::parse($startDateTime)->dayName),
            Carbon::parse($startDateTime)->format('H:i:s'),
            Carbon::parse($endDateTime)->format('H:i:s'),
            $clientName
        );

        return $this->calendarRepository->create($calendarDTO);
    }

    private function isEventCollision($startEventDateTime, $endEventDateTime, $newEventStart, $newEventEnd)
    {
        if ($startEventDateTime->between($newEventStart, $newEventEnd, false) || $endEventDateTime->between($newEventStart, $newEventEnd, false) || $newEventStart->between($startEventDateTime, $endEventDateTime, false)) {
            return response()->json(['error' => 'Overlap between dates']);
        }
    }

    public function checkCollisionForNone($startEventDateTime, $endEventDateTime, $newEventStart, $newEventEnd): bool
    {
        if ($this->isEventCollision($startEventDateTime, $endEventDateTime, $newEventStart, $newEventEnd)) {
            return false;
        }
        return true;
    }
    public function checkCollisionForEveryWeek($newEventStart, $newEventEnd, $futureDateStart, $futureDateEnd, $dayName): bool
    {
        if (Carbon::parse($newEventStart)->dayName === $dayName && $this->isEventCollision($futureDateStart, $futureDateEnd, $newEventStart, $newEventEnd)) {
            return false;
        }
        return true;
    }
    public function checkCollisionForOddWeek($newEventStart, $newEventEnd, $futureDateStart, $futureDateEnd, $dayName): bool
    {
        $currentWeekNumber = Carbon::parse($newEventStart)->week;
        if ($currentWeekNumber % 2 !== 0 && Carbon::parse($newEventStart)->dayName === $dayName && $this->isEventCollision($futureDateStart, $futureDateEnd, $newEventStart, $newEventEnd)) {
            return false;
        }
        return true;
    }
    public function checkCollisionForEvenWeek($newEventStart, $newEventEnd, $futureDateStart, $futureDateEnd, $dayName): bool
    {
        $currentWeekNumber = Carbon::parse($newEventStart)->week;
        if ($currentWeekNumber % 2 === 0 && Carbon::parse($newEventStart)->dayName === $dayName && $this->isEventCollision($futureDateStart, $futureDateEnd, $newEventStart, $newEventEnd)) {
            return false;
        }
        return true;
    }
}
