<?php

namespace UserApi\Api\Controllers;

use Cubex\Controller\Controller;
use Exception;
use Firebase\JWT\JWT;
use Packaged\Http\Responses\JsonResponse;
use UserApi\Context\UserApiContext;
use UserApi\Forms\LoginForm;

class AuthenticationController extends Controller
{

  /**
   * @inheritDoc
   */
  protected function _generateRoutes(): string
  {
    return 'login';
  }

  /**
   * @param UserApiContext $context
   *
   * @return JsonResponse
   * @throws Exception
   */
  public function postLogin(UserApiContext $context): JsonResponse
  {
    $request = json_decode($context->request()->getContent(), true);
    $jwtConfig = $context->getConfig()->getSection('jwt')->getItems();

    $loginForm = new LoginForm();
    $loginForm->hydrate($request);
    $errors = $context->getErrorMessages($loginForm->validate());

    if(count($errors))
    {
      return JsonResponse::create(['errors' => $errors], 400);
    }

    if($request['username'] !== $jwtConfig['username'] || $request['password'] !== $jwtConfig['password'])
    {
      return JsonResponse::create(['error' => 'Wrong credentials!'], 401);
    }

    $host = $context->request()->getHost();
    return JsonResponse::create(
      [
        'token' => JWT::encode(
          [
            'iss' => $host,
            'aud' => $host,
            'iat' => time(),
            'exp' => time() + intval($jwtConfig['exp']),
          ],
          $jwtConfig['key']
        ),
      ]
    );
  }

}
