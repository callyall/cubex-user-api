<?php
define('PHP_START', microtime(true));

use Cubex\Cubex;
use UserApi\Context\UserApiContext;
use UserApi\Api\Application;
use Whoops\Handler\PrettyPageHandler;
use Whoops\Run;

$loader = require_once(dirname(__DIR__) . '/vendor/autoload.php');
try
{
  //Create a global Cubex instance, using SkeletonContext
  $cubex = Cubex::withCustomContext(UserApiContext::class, dirname(__DIR__), $loader);
  //Handle the incoming request through "DefaultApplication"
  $cubex->handle(new Application($cubex));
}
catch(Throwable $e)
{
  $handler = new Run();
  $handler->pushHandler(new PrettyPageHandler());
  $handler->handleException($e);
}
finally
{
  if($cubex instanceof Cubex)
  {
    //Call the shutdown command
    $cubex->shutdown();
  }
}
