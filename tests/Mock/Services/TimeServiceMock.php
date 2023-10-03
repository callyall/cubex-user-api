<?php

namespace UserApi\Tests\Mock\Services;

use DateTime;
use UserApi\Services\TimeServiceInterface;

class TimeServiceMock implements TimeServiceInterface
{
  public function __construct(protected int $year) {}

  public function getTimestamp(): int
  {
    // TODO: Implement getTimestamp() method.
  }

  public function getDateTime(): DateTime
  {
    // TODO: Implement getDateTime() method.
  }

  public function getDayOfTheWeek(): string
  {
    // TODO: Implement getDayOfTheWeek() method.
  }

  public function getDateTimeString(): string
  {
    // TODO: Implement getDateTimeString() method.
  }

  public function getDateString(): string
  {
    // TODO: Implement getDateString() method.
  }

  public function getYear(): int
  {
    return $this->year;
  }

  public function getMonth(): int
  {
    // TODO: Implement getMonth() method.
  }

  public function getDate(): int
  {
    // TODO: Implement getDate() method.
  }

  public function isLeapYear(): bool
  {
    // TODO: Implement isLeapYear() method.
  }

  /**
   * @param int $year
   *
   * @return TimeServiceMock
   */
  public function setYear(int $year): TimeServiceMock
  {
    $this->year = $year;
    return $this;
  }
}
