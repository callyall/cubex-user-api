<?php

namespace UserApi\Api;

use Cubex\Cubex;
use Packaged\Context\Context;
use Packaged\Dal\Exceptions\DataStore\DaoNotFoundException;
use Packaged\DiContainer\DependencyInjector;
use Packaged\Form\Form\Form;
use Packaged\Http\Request;
use Packaged\Http\Responses\JsonResponse;
use ReflectionClass;
use Symfony\Component\HttpFoundation\Response;
use Throwable;
use UserApi\Api\Controllers\AuthenticationController;
use UserApi\Api\Controllers\UserController;
use UserApi\Api\Services\ServiceInterface;
use UserApi\Application as BaseApplication;
use UserApi\DependencyResolver\DependencyResolverInterface;

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

  protected function _configureConnections()
  {
    parent::_configureConnections();

    $ctx = $this->getContext();
    $cubex = $this->getCubex();

    $cubex->share(Request::class, $ctx->request(), DependencyInjector::MODE_IMMUTABLE);

    $cubex->factory(Form::class, function ($className) use ($ctx) {
      /** @var Form $form */
      $form = new $className();
      $form->hydrate(json_decode($ctx->request()->getContent(), true));

      return $form;
    });

    // Retrieve services and instantiate them with specific constructor parameters
    $cubex->factory(ServiceInterface::class, function ($className) use ($ctx, $cubex) {
      /** @var $resolver DependencyResolverInterface */
      $resolver = $cubex->retrieve(DependencyResolverInterface::class);
      return new $className(...$resolver->getDependencyInstances(new ReflectionClass($className)));
    });
  }

}
