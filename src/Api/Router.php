<?php

namespace UserApi\Api;

use Cubex\Controller\Controller;
use Exception;
use Packaged\Context\Context;
use Packaged\Http\Responses\JsonResponse;
use Packaged\Routing\Handler\Handler;
use Protectednet\DependencyResolver\DependencyResolverInterface;
use UserApi\Api\Controllers\BarController;
use UserApi\Api\Controllers\DependencyInjection\TimeController;
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

    yield self::_route('/di/time', TimeController::class);

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

  /**
   * @param UserApiContext $c
   * @param mixed          $handler
   *
   * @return array|callable|mixed|Handler|string
   * @throws Exception
   */
  protected function _prepareHandler(Context $c, $handler)
  {
    if (is_string($handler) && str_contains($handler, '\\') && class_exists($handler))
    {
      /** @var DependencyResolverInterface $dependencyResolver */
      $dependencyResolver = $c->getCubex()->retrieve(DependencyResolverInterface::class);

      return new $handler(
        ...$dependencyResolver->getDependencyInstances($handler)
      );
    }

    return parent::_prepareHandler($c, $handler);
  }
}
