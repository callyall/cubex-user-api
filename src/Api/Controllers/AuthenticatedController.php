<?php

namespace UserApi\Api\Controllers;

use Cubex\Controller\Controller;
use Packaged\Context\Context;
use Packaged\Http\Responses\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use UserApi\Context\UserApiContext;

abstract class AuthenticatedController extends AbstractController
{

  /**
   * @param Context $c
   *
   * @return JsonResponse
   * @throws \Throwable
   */
  public function handle(Context $c): Response
  {
    /**
     * @var $c UserApiContext
     */
    if(!$c->isAuthenticated())
    {
      return JsonResponse::create(['error' => 'Not authenticated'], 403);
    }
    return parent::handle($c);
  }
}
