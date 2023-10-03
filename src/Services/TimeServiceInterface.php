<?php

namespace UserApi\Services;

use DateTime;

interface TimeServiceInterface
{
  public function getTimestamp(): int;

  public function getDateTime(): DateTime;

  public function getDayOfTheWeek(): string;

  public function getDateTimeString(): string;

  public function getDateString(): string;

  public function getYear(): int;

  public function getMonth(): int;

  public function getDate(): int;

  public function isLeapYear(): bool;
}
