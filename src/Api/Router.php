<?php

namespace UserApi\Api;

use Cubex\Controller\Controller;
use Packaged\Http\Responses\JsonResponse;
use UserApi\Api\Controllers\BarController;
use UserApi\Api\Controllers\HelloController;
use UserApi\Context\UserApiContext;

class Router extends Controller
{

  protected function _generateRoutes()
  {
    // Delegates the route to another controller
    yield self::_route('/hello', HelloController::class);

    // Delegates the route to another controller
    yield self::_route('/foo/bar', BarController::class);

    // Delegates the route to the getFoo method
    yield self::_route('/foo', 'foo');

    // Anything else will go to the getDefault method
    return 'default';
  }

  public function getFoo(UserApiContext $context): JsonResponse
  {
    return JsonResponse::create(['message' => "A response from {$context->request()->path()}"]);
  }

  public function getDefault(): JsonResponse
  {
    return JsonResponse::create(['message' => 'This is the default route']);
  }
}
