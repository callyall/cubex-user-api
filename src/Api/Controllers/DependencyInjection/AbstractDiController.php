<?php

namespace UserApi\Api\Controllers\DependencyInjection;

use Cubex\Controller\Controller;
use Packaged\Context\Context;
use Protectednet\DependencyResolver\DependencyResolverInterface;
use UserApi\Context\UserApiContext;

abstract class AbstractDiController extends Controller
{
  /**
   * @param UserApiContext $c
   * @param mixed          $handler
   * @param mixed          $response
   *
   * @return bool
   * @throws \Exception
   */
  protected function _processHandler(Context $c, $handler, &$response): bool
  {
    while (is_callable($handler))
    {
      /**
       * Checks if this is a handler method in this controller
       * and if it is it will pass the necessary dependencies to it
       */
      if (is_array($handler) || $handler[0] instanceof $this)
      {
        /** @var DependencyResolverInterface $dependencyResolver */
        $dependencyResolver = $c->getCubex()->retrieve(DependencyResolverInterface::class);

        /** $handler is a callable array that holds an instance of the controller and a method name */
        $handler = $handler(
        /**
         * We pass the class name and the method name to the dependency resolver.
         * It returns back an array of dependencies that we pass to the method call
         */
          ...$dependencyResolver
            ->getDependencyInstances(get_class($handler[0]), $handler[1])
        );
      }
    }

    return parent::_processHandler($c, $handler, $response);
  }

}
