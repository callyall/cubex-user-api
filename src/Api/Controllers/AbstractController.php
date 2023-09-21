<?php

namespace UserApi\Api\Controllers;

use Cubex\Controller\Controller;
use Packaged\DiContainer\DependencyInjector;
use ReflectionClass;
use UserApi\Context\UserApiContext;
use UserApi\DependencyResolver\DependencyResolverInterface;

abstract class AbstractController extends Controller
{
  /**
   * @param UserApiContext $c
   * @param mixed          $handler
   * @param mixed          $response
   *
   * @return bool
   * @throws \ReflectionException|\Exception
   */
  protected function _processHandler($c, $handler, &$response): bool
  {
    // Share route data
    foreach($c->routeData()->all() as $key => $value)
    {
      $c->getCubex()->share($key, $value, DependencyInjector::MODE_IMMUTABLE);
    }

    /**
     * @var DependencyResolverInterface $resolver
     */
    $resolver = $c
      ->getCubex()
      ->retrieve(DependencyResolverInterface::class);

    while(is_callable($handler))
    {
      $handler = $handler(
        ...$resolver
          ->getDependencyInstances((new ReflectionClass($handler[0])), $handler[1])
      );
    }

    return parent::_processHandler($c, $handler, $response);
  }
}
