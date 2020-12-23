<?php

namespace UserApi\Controllers;

use Cubex\Controller\Controller;
use Firebase\JWT\JWT;
use Packaged\Context\Context;
use Packaged\Http\Response;
use UserApi\Context\UserApiContext;
use UserApi\Forms\LoginForm;

class AuthenticationController extends Controller
{

  /**
   * @inheritDoc
   */
  protected function _generateRoutes()
  {
    return 'login';
  }

  /**
   * @param UserApiContext $context
   *
   * @return Response
   * @throws \Exception
   */
  public function postLogin(UserApiContext $context): Response
  {
    $request = json_decode($context->request()->getContent(), true);
    $jwtConfig = $context->getConfig()->getSection('jwt')->getItems();

    if(!array_key_exists('username', $jwtConfig) || !array_key_exists('password', $jwtConfig))
    {
      return Response::create(
        json_encode(['error' => 'Something went wrong!']),
        500,
        ['Content-Type' => 'application/json']
      );
    }

    $loginForm = new LoginForm();
    $errors = $loginForm->hydrate($request);

    if(!$loginForm->isValid())
    {
      return Response::create(json_encode(['errors' => $errors]), 400, ['Content-Type' => 'application/json']);
    }

    if($request['username'] !== $jwtConfig['username'] || $request['password'] !== $jwtConfig['password'])
    {
      return Response::create(
        json_encode(['error' => 'Wrong credentials!']),
        401,
        ['Content-Type' => 'application/json']
      );
    }

    $host = $context->request()->getHost();
    $payload = [
      'iss' => $host,
      'aud' => $host,
      'iat' => time(),
      'exp' => time() + intval($jwtConfig['exp']),
    ];
    $jwt = JWT::encode($payload, $jwtConfig['key']);

    return Response::create(json_encode(['token' => $jwt]), 200, ['Content-Type' => 'application/json']);
  }

}