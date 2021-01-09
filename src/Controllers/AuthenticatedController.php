<?php

namespace UserApi\Controllers;

use Cubex\Controller\Controller;
use Packaged\Context\Context;
use Packaged\Http\Response;
use UserApi\Context\UserApiContext;

abstract class AuthenticatedController extends Controller
{

  /**
   * @param Context $c
   *
   * @return \Symfony\Component\HttpFoundation\Response
   * @throws \Throwable
   */
  public function handle(Context $c): \Symfony\Component\HttpFoundation\Response
  {
    /**
     * @var $c UserApiContext
     */
    if(!$c->isAuthenticated())
    {
      return Response::create(
        json_encode(['error' => 'Not authenticated']),
        403,
        ['Content-Type' => 'application/json']
      );
    }
    return parent::handle($c);
  }
}