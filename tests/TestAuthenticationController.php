<?php

use Cubex\Cubex;
use Packaged\Http\Request;
use PHPUnit\Framework\TestCase;
use UserApi\Api\Application;
use UserApi\Context\UserApiContext;

class TestAuthenticationController extends TestCase
{
  public function testLogin()
  {
    $cubex = Cubex::withCustomContext(UserApiContext::class, dirname(__DIR__), null, false);

    $request = Request::create(
      '/login',
      'POST',
      [],
      [],
      [],
      [],
      json_encode(['username' => 'admin', 'password' => '1234'])
    );

    $ctx= new UserApiContext($request);
    $cubex->prepareContext($ctx);
    $app = new Application($cubex);
    $response = $app->handle($ctx);

    $this->assertArrayHasKey('token', json_decode($response->getContent(), true));
  }

}