<?php

namespace UserApi;

use Cubex\Application\Application as CubeXApplication;
use Packaged\Config\ConfigProviderInterface;
use Packaged\Config\Provider\Ini\IniConfigProvider;
use Packaged\Dal\DalResolver;
use Packaged\DiContainer\DependencyInjector;
use Packaged\Helpers\Path;
use UserApi\DependencyResolver\DependencyResolver;
use UserApi\DependencyResolver\DependencyResolverInterface;

abstract class Application extends CubexApplication
{
  //Setup our database connections
  protected function _configureConnections()
  {
    $ctx = $this->getContext();
    $cubex = $this->getCubex();
    $confDir = Path::system($ctx->getProjectRoot(), 'conf');

    $configProvider = new IniConfigProvider();
    $configProvider->loadFiles(
      [
        $confDir . DIRECTORY_SEPARATOR . 'connections.ini',
        $confDir . DIRECTORY_SEPARATOR . $ctx->getEnvironment() . DIRECTORY_SEPARATOR . 'connections.ini',
      ]
    );
    $datastoreConfig = new IniConfigProvider();
    $datastoreConfig->loadFiles(
      [
        $confDir . DIRECTORY_SEPARATOR . 'defaults' . DIRECTORY_SEPARATOR . 'datastores.ini',
        $confDir . DIRECTORY_SEPARATOR . $ctx->getEnvironment() . DIRECTORY_SEPARATOR . 'datastores.ini',
      ]
    );
    $resolver = new DalResolver($configProvider, $datastoreConfig);
    $cubex->share(DalResolver::class, $resolver);
    $resolver->boot();
    $cubex->share(ConfigProviderInterface::class, $ctx->getConfig());
    $cubex->share(DependencyResolverInterface::class, new DependencyResolver($this->getCubex()), DependencyInjector::MODE_IMMUTABLE);
  }
}
