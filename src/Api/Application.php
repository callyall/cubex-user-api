<?php

namespace UserApi\Api;

use Cubex\Cubex;
use UserApi\Controllers\IndexController;
use UserApi\Application as BaseApplication;

class Application extends BaseApplication
{

  public function __construct(Cubex $cubex)
  {
    parent::__construct($cubex);
    // Convert errors into exceptions
    set_error_handler(
      function ($errno, $errstr, $errfile, $errline) {
        if((error_reporting() & $errno) && !($errno & E_NOTICE))
        {
          throw new \ErrorException($errstr, 0, $errno, str_replace(dirname(__DIR__), '', $errfile), $errline);
        }
      }
    );

  }

  protected function _generateRoutes()
  {
    yield self::_route('/', IndexController::class);

    //Let the parent application handle routes from here
    return parent::_generateRoutes();
  }

}