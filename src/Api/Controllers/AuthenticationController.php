<?php

namespace UserApi\Api\Controllers;

use Exception;
use Packaged\Http\Responses\JsonResponse;
use UserApi\Api\Services\AuthenticationService;

class AuthenticationController extends AbstractController
{

  /**
   * @inheritDoc
   */
  protected function _generateRoutes(): string
  {
    return 'login';
  }

  /**
   * @param AuthenticationService $service
   *
   * @return JsonResponse
   * @throws Exception
   */
  public function postLogin(AuthenticationService $service): JsonResponse
  {
    $errors = $service->getValidationErrors();

    if(!empty($errors))
    {
      return JsonResponse::create(['errors' => $errors], 400);
    }

    $result = $service->authenticate();

    return empty($result)
      ? JsonResponse::create(['error' => 'Wrong credentials!'], 401)
      : JsonResponse::create(['token' => $result]);
  }

}
