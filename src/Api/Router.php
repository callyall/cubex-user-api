<?php

namespace UserApi\Api;

use Cubex\Controller\Controller;
use UserApi\Api\Controllers\AuthenticationController;
use UserApi\Api\Controllers\CustomerController;
use UserApi\Api\Controllers\UserController;

class Router extends Controller
{

  protected function _generateRoutes()
  {
//    yield self::_route('/user', UserController::class);
//    yield self::_route('/login', AuthenticationController::class);
      yield self::_route('/customer', CustomerController::class);
  }
}
