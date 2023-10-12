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

    $cubex = $this->getCubex();

    $cubex
      /**
       * This is a shared instance.
       * It will be stored in the container for the duration of the request.
       * Cubex will return it every time a TimeService is requested.
       */
      ->share(
        /** The name of the class/interface we want to store the instance against.*/
        TimeService::class,
        new TimeService(Carbon::now(), $this->getContext()->request()->query->getInt('subDays')),
      )
      /**
       * The factory on the other hand will only be called if we specifically request
       * an instance of TimeServiceInterface.
       *
       * We can get the same instance from it every time or we can explicitly request
       * a new instance to be created upon request.
       */
      ->factory(
        /** The name of the class/interface we want to store the instance against.*/
        TimeServiceInterface::class,
        fn() => new TimeService(Carbon::now(), $this->getContext()->request()->query->getInt('subDays'))
      );

    /**
     * This will return the shared instance of TimeService.
     */
    $cubex->retrieve(TimeService::class);

    /**
     * This will execute the factory and return an instance of TimeServiceInterface.
     */
    $cubex->retrieve(TimeServiceInterface::class);
    /**
     * This will return the same instance of TimeServiceInterface as the previous call.
     *
     * The factory won't be called again!!!
     */
    $cubex->retrieve(TimeServiceInterface::class);

    /**
     * This will execute the factory and return a new instance of TimeServiceInterface.
     */
    $cubex->retrieve(TimeServiceInterface::class, shared: false);
  }

  protected function _defaultHandler(): Handler
  {
    return new Router();
  }
}
