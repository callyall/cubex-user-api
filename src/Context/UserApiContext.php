<?php

namespace UserApi\Context;

use Carbon\Carbon;
use Cubex\Context\Context;
use UserApi\Services\TimeService;
use UserApi\Services\TimeServiceInterface;

class UserApiContext extends Context
{
  private ?TimeServiceInterface $timeService = null;

  public function getPersistentTimeService(): TimeServiceInterface
  {
    if (!$this->timeService)
    {
      $this->timeService = new TimeService(Carbon::now(), $this->request()->query->getInt('subDays'));
    }

    return $this->timeService;
  }

  public function getTimeService(): TimeServiceInterface
  {
    return new TimeService(Carbon::now(), $this->request()->query->getInt('subDays'));
  }
}
