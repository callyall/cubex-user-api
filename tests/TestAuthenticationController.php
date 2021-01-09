<?php

use Cubex\Cubex;
use Packaged\Http\Request;
use PHPUnit\Framework\TestCase;
use UserApi\Api\Application;
use UserApi\Context\UserApiContext;

class TestAuthenticationController extends TestCase
{

  protected Application $_app;

  protected function setUp()
  {
    $this->_app = new Application(Cubex::withCustomContext(UserApiContext::class, dirname(__DIR__), null, false));
  }

  protected function _proccessRequest(Request $request)
  {
    $context = new UserApiContext($request);
    $this->_app->getCubex()->prepareContext($context);
    return $this->_app->handle($context);
  }

  public function testLogin()
  {
    // Valid data
    $request = Request::create(
      '/login',
      'POST',
      [],
      [],
      [],
      [],
      json_encode(['username' => 'admin', 'password' => '1234'])
    );

    $response = $this->_proccessRequest($request);

    $this->assertEquals(200, $response->getStatusCode());
    $this->assertArrayHasKey('token', json_decode($response->getContent(), true));

    // Invalid data
    $request = Request::create(
      '/login',
      'POST',
      [],
      [],
      [],
      [],
      json_encode(['password' => '1234'])
    );

    $response = $this->_proccessRequest($request);

    $this->assertEquals(400, $response->getStatusCode());

    // Wrong credentials
    $request = Request::create(
      '/login',
      'POST',
      [],
      [],
      [],
      [],
      json_encode(['username' => 'admiiin', 'password' => '1234'])
    );

    $response = $this->_proccessRequest($request);

    $this->assertEquals(401, $response->getStatusCode());

  }

}