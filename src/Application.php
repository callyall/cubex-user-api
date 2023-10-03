<?php

namespace UserApi;

use Cubex\Application\Application as CubeXApplication;
use Protectednet\DependencyResolver\Cache\ApcuCacheProvider;
use Protectednet\DependencyResolver\DependencyResolver;
use Protectednet\DependencyResolver\DependencyResolverInterface;

abstract class Application extends CubexApplication
{
  protected function _initialize(): void
  {
    $cubex = $this->getCubex();

    $cubex
      ->factory(
        DependencyResolverInterface::class,
        fn() => new DependencyResolver($cubex, new ApcuCacheProvider())
      );
  }

}
