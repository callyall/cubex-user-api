<?php

namespace UserApi\Api;

use Carbon\Carbon;
use Cubex\Cubex;
use Packaged\Routing\Handler\Handler;
use UserApi\Application as BaseApplication;
use UserApi\Services\TimeService;
use UserApi\Services\TimeServiceInterface;

class Application extends BaseApplication
{

  public function __construct(Cubex $cubex)
  {
    parent::__construct($cubex);
  }

  protected function _initialize(): void
  {
    parent::_initialize();

    $this
      ->getCubex()
      ->factory(
        TimeService::class,
        fn() => new TimeService(Carbon::now(), $this->getContext()->request()->query->getInt('subDays'))
      )
      ->factory(
        TimeServiceInterface::class,
        fn() => new TimeService(Carbon::now(), $this->getContext()->request()->query->getInt('subDays'))
      );
  }

  protected function _defaultHandler(): Handler
  {
    return new Router();
  }
}
