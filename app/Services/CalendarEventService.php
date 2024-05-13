<?php

namespace App\Services;

use App\DTO\CalendarEventDTO;
use App\Enums\RepeatType;
use App\Factories\CalendarEventFactory;
use App\Repositories\CalendarEventRepository;
use Carbon\Carbon;

class CalendarEventService
{
    private CalendarEventRepository $calendarEventRepository;
    private DayService $dayService;
    private CalendarEventFactory $calendarEventFactory;

    public function __construct(
        CalendarEventRepository $calendarRepository,
        DayService $dayService,
        CalendarEventFactory $calendarEventFactory
    ) {
        $this->calendarEventRepository = $calendarRepository;
        $this->dayService = $dayService;
        $this->calendarEventFactory = $calendarEventFactory;
    }

    public function fetchCalendar(): array
    {
        $calendarEvents = $this->calendarEventRepository->getCalendar();
        $calendarEventDetails = [];

        foreach ($calendarEvents as $event) {
            $calendarEventDetails[] = $this->calendarEventFactory->create($event);
        }

        return $calendarEventDetails;
    }

    public function checkCalendar(string $startDateTime, string $endDateTime): bool
    {
        $calendarEvents = $this->calendarEventRepository->getCalendar();

        $formatedStartDateTime = Carbon::create($startDateTime)->format('Y-m-d H:i:s');
        $formatedEndDateTime = Carbon::create($endDateTime)->format('Y-m-d H:i:s');

        $newEventStart = Carbon::create($formatedStartDateTime);
        $newEventEnd = Carbon::create($formatedEndDateTime);

        foreach ($calendarEvents as $event) {
            $startEventDateTime = Carbon::parse($event->start_date . ' ' . $event->start_time);
            $endEventDateTime = Carbon::parse(($event->end_date ?: $event->start_date) . ' ' . $event->end_time);

            $dayName = $this->dayService->getNameFromNumber($event->day);

            $futureDateStart = Carbon::create($newEventStart->format('Y-m-d') . ' ' . $event->start_time);
            $futureDateEnd = Carbon::create($newEventEnd->format('Y-m-d') . ' ' . $event->end_time);

            $collision = match ($event->repeat) {
                RepeatType::NONE->value => $this->checkCollisionForNone(
                    $startEventDateTime, $endEventDateTime, $newEventStart, $newEventEnd
                ),
                RepeatType::EVERY_WEEK->value => $this->checkCollisionForEveryWeek(
                    $newEventStart, $newEventEnd, $futureDateStart, $futureDateEnd, $dayName
                ),
                RepeatType::ODD_WEEK->value => $this->checkCollisionForOddOrEvenWeek(
                    $newEventStart, $newEventEnd, $futureDateStart, $futureDateEnd, $dayName
                ),
                RepeatType::EVEN_WEEK->value => $this->checkCollisionForOddOrEvenWeek(
                    $newEventStart, $newEventEnd, $futureDateStart, $futureDateEnd, $dayName, false
                ),
            };

            if (!$collision) {
                return false;
            }
        }
        return true;
    }

    public function create(string $startDateTime, string $endDateTime, string $clientName): mixed
    {
        $calendarEventDTO = new CalendarEventDTO(
            Carbon::parse($startDateTime)->format('Y-m-d'),
            Carbon::parse($endDateTime)->format('Y-m-d'),
            0,
            $this->dayService->getNameFromString(Carbon::parse($startDateTime)->dayName),
            Carbon::parse($startDateTime)->format('H:i:s'),
            Carbon::parse($endDateTime)->format('H:i:s'),
            $clientName
        );

        return $this->calendarEventRepository->create($calendarEventDTO);
    }

    private function isEventCollision(Carbon $startEventDateTime, Carbon $endEventDateTime, Carbon $newEventStart, Carbon $newEventEnd): bool
    {
        if ($startEventDateTime->between($newEventStart, $newEventEnd, false)
            || $endEventDateTime->between($newEventStart, $newEventEnd, false)
            || $newEventStart->between($startEventDateTime, $endEventDateTime, false)
            || $newEventEnd->between($startEventDateTime, $endEventDateTime, false)
        ) {
            return true;
        }
        return false;
    }

    public function checkCollisionForNone(Carbon $startEventDateTime, Carbon $endEventDateTime, Carbon $newEventStart, Carbon $newEventEnd): bool
    {
        return !($this->isEventCollision($startEventDateTime, $endEventDateTime, $newEventStart, $newEventEnd));
    }

    public function checkCollisionForEveryWeek(
        Carbon $newEventStart, Carbon $newEventEnd, Carbon $futureDateStart, Carbon $futureDateEnd, string $dayName
    ): bool {
        return !(Carbon::parse($newEventStart)->dayName === $dayName
            && $this->isEventCollision($futureDateStart, $futureDateEnd, $newEventStart, $newEventEnd));
    }

    public function checkCollisionForOddOrEvenWeek(
        Carbon $newEventStart, Carbon $newEventEnd, Carbon $futureDateStart, Carbon $futureDateEnd, string $dayName, bool $isOddWeek = true
    ): bool {
        $currentWeekNumber = Carbon::parse($newEventStart)->week;
        if (($isOddWeek && $currentWeekNumber % 2 !== 0) || (!$isOddWeek && $currentWeekNumber % 2 === 0)) {
            return !(Carbon::parse($newEventStart)->dayName === $dayName
                && $this->isEventCollision($futureDateStart, $futureDateEnd, $newEventStart, $newEventEnd));
        }
        return true;
    }
}
