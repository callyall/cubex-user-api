<?php

namespace UserApi\Controllers;

use Cubex\Controller\Controller;
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
   * @throws \Exception
   */
  public function postLogin(UserApiContext $context): JsonResponse
  {
    $request = json_decode($context->request()->getContent(), true);
    $jwtConfig = $context->getConfig()->getSection('jwt')->getItems();

    if(!array_key_exists('username', $jwtConfig) || !array_key_exists('password', $jwtConfig))
    {
      return JsonResponse::create(['error' => 'Something went wrong!'], 500);
    }

    $loginForm = new LoginForm();
    $errors = $loginForm->hydrate($request);

    if(!$loginForm->isValid())
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