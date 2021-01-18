<?php

use Cubex\Cubex;
use Packaged\Http\Request;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Response;
use UserApi\Api\Application;
use UserApi\Context\UserApiContext;

class TestAuthenticationController extends TestCase
{

  protected Application $_app;

  protected function setUp()
  {
    $this->_app = new Application(Cubex::withCustomContext(UserApiContext::class, dirname(__DIR__), null, false));
  }

  protected function _prepareRequest(array $data): Request
  {
    return Request::create(
      '/login',
      'POST',
      [],
      [],
      [],
      [],
      json_encode($data)
    );
  }

  protected function _proccessRequest(Request $request): Response
  {
    $context = new UserApiContext($request);
    $this->_app->getCubex()->prepareContext($context);
    return $this->_app->handle($context);
  }

  public function testLogin()
  {
    // Valid data
    $response = $this->_proccessRequest($this->_prepareRequest(['username' => 'admin', 'password' => '1234']));
    $this->assertEquals(200, $response->getStatusCode());
    $this->assertArrayHasKey('token', json_decode($response->getContent(), true));

    // Invalid data
    $response = $this->_proccessRequest($this->_prepareRequest(['password' => '1234']));
    $this->assertEquals(400, $response->getStatusCode());

    // Wrong credentials
    $response = $this->_proccessRequest($this->_prepareRequest(['username' => 'admiiin', 'password' => '1234']));
    $this->assertEquals(401, $response->getStatusCode());
  }

}
