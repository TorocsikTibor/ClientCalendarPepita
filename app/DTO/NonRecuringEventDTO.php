<?php

namespace App\DTO;

use JsonSerializable;

class NonRecuringEventDTO implements JsonSerializable
{
    private string $title;
    private string $start;
    private string $end;

    public function __construct(string $title, string $start, string $end)
    {
        $this->title = $title;
        $this->start = $start;
        $this->end = $end;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getStart(): string
    {
        return $this->start;
    }

    public function getEnd(): string
    {
        return $this->end;
    }

    public function jsonSerialize(): array
    {
        return [
            'title' => $this->title,
            'start' => $this->start,
            'end' => $this->end,
        ];
    }
}
