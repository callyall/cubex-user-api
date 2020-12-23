<?php

namespace UserApi\Api;

use Cubex\Context\Context;
use Cubex\Cubex;
use UserApi\Controllers\AuthenticationController;
use UserApi\Controllers\IndexController;
use UserApi\Application as BaseApplication;
use UserApi\Controllers\UserController;

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
    $this->setContext($cubex->getContext());
    $this->_configureConnections();
  }



  protected function _generateRoutes()
  {
    yield self::_route('/user', UserController::class);
    yield self::_route('/login', AuthenticationController::class);

    return parent::_generateRoutes();
  }

}