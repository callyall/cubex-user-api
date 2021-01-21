<?php

namespace UserApi\Api;

use Cubex\Cubex;
use Packaged\Context\Context;
use Packaged\Dal\Exceptions\DataStore\DaoNotFoundException;
use Packaged\Http\Responses\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Throwable;
use UserApi\Api\Controllers\AuthenticationController;
use UserApi\Api\Controllers\UserController;
use UserApi\Application as BaseApplication;

class Application extends BaseApplication
{

  public function __construct(Cubex $cubex)
  {
    parent::__construct($cubex);
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
      $c->log()->error($e);
      return JsonResponse::create(['error' => 'Resource not found!'], 404);
    }
    catch(Throwable $e)
    {
      $c->log()->error($e);
      return JsonResponse::create(['error' => 'Something went wrong!'], 500);
    }
  }

}
