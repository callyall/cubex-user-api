<?php

namespace UserApi\Api;

use Cubex\Controller\Controller;
use Exception;
use Packaged\Context\Context;
use Packaged\Http\Responses\JsonResponse;
use Packaged\Routing\Handler\Handler;
use Protectednet\DependencyResolver\DependencyResolverInterface;
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

    // Must be below the above otherwise it will match first
    yield self::_route('/{name}', 'name');

    // Anything else will go to the getDefault method
    return 'default';
  }

  public function getName(): JsonResponse
  {
    return JsonResponse::create(['message' => 'Hello ' . $this->getContext()->routeData()->get('name')]);
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
