<?php

namespace UserApi\Api;

use Cubex\Cubex;
use ErrorException;
use Exception;
use Packaged\Context\Context;
use Packaged\Dal\Exceptions\DataStore\DaoNotFoundException;
use Packaged\Http\Responses\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use UserApi\Api\Controllers\AuthenticationController;
use UserApi\Api\Controllers\UserController;
use UserApi\Application as BaseApplication;

class Application extends BaseApplication
{

  public function __construct(Cubex $cubex)
  {
    parent::__construct($cubex);
    // Convert errors into exceptions
    set_error_handler(
      function ($errno, $errstr, $errfile, $errline) {
        if((error_reporting() & $errno) && !($errno & E_NOTICE))
        {
          throw new ErrorException($errstr, 0, $errno, str_replace(dirname(__DIR__), '', $errfile), $errline);
        }
      }
    );
    $this->setContext($cubex->getContext());
    $this->_configureConnections();
  }

  protected function _generateRoutes()
  {
    yield self::_route('/user', UserController::class);
    yield self::_route('/login', AuthenticationController::class);

    return parent::_generateRoutes();
  }

  public function handle(Context $c): Response
  {
    try
    {
      return parent::handle($c);
    }
    catch(DaoNotFoundException $e)
    {
      return JsonResponse::create(['error' => 'Resource not found!'], 404);
    }
    catch(Exception $e)
    {
      return JsonResponse::create(['error' => 'Something went wrong!'], 500);
    }
  }

}
