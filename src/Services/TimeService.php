<?php

namespace UserApi\Services;

use Carbon\Carbon;
use DateTime;

readonly class TimeService implements TimeServiceInterface
{
  public function __construct(protected Carbon $now, int $subDays)
  {
    $this->now->subDays($subDays);
  }

  public function getTimestamp(): int
  {
    return $this->now->timestamp;
  }

  public function getDateTime(): DateTime
  {
    return $this->now->toDateTime();
  }

  public function getDayOfTheWeek(): string
  {
    return $this->now->dayName;
  }

  public function getDateTimeString(): string
  {
    return $this->now->toDateTimeString();
  }

  public function getDateString(): string
  {
    return $this->now->toDateString();
  }

  public function getYear(): int
  {
    return $this->now->year;
  }

  public function getMonth(): int
  {
    return $this->now->month;
  }

  public function getDate(): int
  {
    return $this->now->day;
  }

  public function isLeapYear(): bool
  {
    return $this->now->isLeapYear();
  }
}
