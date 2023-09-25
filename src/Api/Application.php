<?php

namespace UserApi\Api;

use Cubex\Cubex;
use Packaged\Routing\Handler\Handler;
use UserApi\Application as BaseApplication;

class Application extends BaseApplication
{

  public function __construct(Cubex $cubex)
  {
    parent::__construct($cubex);
  }

  protected function _initialize()
  {
    // Some database or config setup goes here
  }

  protected function _defaultHandler(): Handler
  {
    return new Router();
  }
}
