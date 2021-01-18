<?php

namespace UserApi\Cli;

use Cubex\Context\Context;
use UserApi\Application as BaseApplication;

class Application extends BaseApplication
{
  public static function launch(Context $ctx)
  {
    $app = new static($ctx->getCubex());
    $app->setContext($ctx);
    $app->_configureConnections();
    return $app;
  }

}
