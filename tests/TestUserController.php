<?php

use Cubex\Cubex;
use Firebase\JWT\JWT;
use Packaged\Http\Request;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Response;
use UserApi\Api\Application;
use UserApi\Context\UserApiContext;

class TestUserController extends TestCase
{
  protected Application $_app;
  protected string $_jwt;

  protected function setUp()
  {
    $this->_app = new Application(Cubex::withCustomContext(UserApiContext::class, dirname(__DIR__), null, false));
    $jwtConfig = $this->_app->getContext()->getConfig()->getSection('jwt')->getItems();
    $payload = [
      'iss' => 'localhost',
      'aud' => 'localhost',
      'iat' => time(),
      'exp' => time() + intval($jwtConfig['exp']),
    ];
    $this->_jwt = JWT::encode($payload, $jwtConfig['key']);
  }

  protected function _prepareRequest(string $uri, string $method = 'GET', bool $authenticated = false, array $data = []
  ): Request
  {

    $request = Request::create(
      $uri,
      $method,
      $method === 'GET' && count($data) ? $data : [],
      [],
      [],
      [],
      json_encode($method !== 'GET' && count($data) ? $data : [])
    );

    if($authenticated)
    {
      $request->headers->add(['token' => $this->_jwt]);
    }

    return $request;
  }

  protected function _proccessRequest(Request $request): Response
  {
    $context = new UserApiContext($request);
    $this->_app->getCubex()->prepareContext($context);
    return $this->_app->handle($context);
  }

  protected function _findExistingUser(): array
  {
    $users = json_decode($this->_proccessRequest($this->_prepareRequest('/user', 'GET', true))->getContent(), true);
    return $users[count($users) - 1];
  }

  public function testNotAuthenticated()
  {
    $response = $this->_proccessRequest($this->_prepareRequest('/user'));
    $this->assertEquals(403, $response->getStatusCode());
  }

  public function testAuthenticated()
  {
    // Request with token query parameter
    $this->assertEquals(
      200,
      $this->_proccessRequest($this->_prepareRequest('/user', 'GET', false, ['token' => $this->_jwt]))->getStatusCode()
    );

    // Request with token header
    $this->assertEquals(200, $this->_proccessRequest($this->_prepareRequest('/user', 'GET', true))->getStatusCode());
  }

  public function testGetIndex()
  {
    // simple request
    $response = $this->_proccessRequest($this->_prepareRequest('/user', 'GET', true));
    $this->assertEquals(200, $response->getStatusCode());
    $this->assertIsArray(json_decode($response->getContent(), true));

    // request with limit search parameter
    $response = $this->_proccessRequest($this->_prepareRequest('/user', 'GET', true, ['limit' => 2]));
    $this->assertEquals(200, $response->getStatusCode());
    $this->assertEquals(2, count(json_decode($response->getContent(), true)));

    // request with firstName search parameter
    $response = $this->_proccessRequest($this->_prepareRequest('/user', 'GET', true, ['firstName' => 'Nikolay']));
    $this->assertEquals(200, $response->getStatusCode());
    $this->assertEquals('Nikolay', json_decode($response->getContent(), true)[0]['firstName']);

    // request with lastName search parameter
    $response = $this->_proccessRequest($this->_prepareRequest('/user', 'GET', true, ['lastName' => 'Brankov']));
    $this->assertEquals(200, $response->getStatusCode());
    $this->assertEquals('Brankov', json_decode($response->getContent(), true)[0]['lastName']);

    // request with username search parameter
    $response = $this->_proccessRequest($this->_prepareRequest('/user', 'GET', true, ['username' => 'NB']));
    $this->assertEquals(200, $response->getStatusCode());
    $this->assertEquals('NB', json_decode($response->getContent(), true)[0]['username']);

    // request with darkMode=true
    $response = $this->_proccessRequest($this->_prepareRequest('/user', 'GET', true, ['darkMode' => 'true']));
    $this->assertEquals(200, $response->getStatusCode());
    $users = json_decode($response->getContent(), true);
    foreach($users as $user)
    {
      $this->assertEquals(true, $user['darkMode']);
    }

    // request with darkMode=false
    $response = $this->_proccessRequest($this->_prepareRequest('/user', 'GET', true, ['darkMode' => 'false']));
    $this->assertEquals(200, $response->getStatusCode());
    $users = json_decode($response->getContent(), true);
    foreach($users as $user)
    {
      $this->assertEquals(false, $user['darkMode']);
    }
  }

  public function testPostIndex()
  {
    // valid data
    $this->assertEquals(
      200,
      $this->_proccessRequest(
        $this->_prepareRequest(
          '/user',
          'POST',
          true,
          [
            'firstName' => 'Test',
            'lastName'  => 'User',
            'username'  => 'TestUser',
            'darkMode'  => true,
          ]
        )
      )->getStatusCode()
    );

    // invalid data
    $this->assertEquals(
      400,
      $this->_proccessRequest(
        $this->_prepareRequest(
          '/user',
          'POST',
          true,
          [
            'firstName' => 'Test',
            'lastName'  => 'User',
          ]
        )
      )->getStatusCode()
    );
  }

  public function testGetUser()
  {
    // existing user
    $this->assertEquals(
      200,
      $this->_proccessRequest(
        $this->_prepareRequest('/user/' . $this->_findExistingUser()['id'], 'GET', true)
      )->getStatusCode()
    );

    // non existing user
    $this->assertEquals(
      404,
      $this->_proccessRequest($this->_prepareRequest('/user/2222', 'GET', true))->getStatusCode()
    );
  }

  public function testDeleteUser()
  {
    // existing user
    $this->assertEquals(
      200,
      $this->_proccessRequest(
        $this->_prepareRequest('/user/' . $this->_findExistingUser()['id'], 'DELETE', true)
      )->getStatusCode()
    );

    // non existing user
    $this->assertEquals(
      404,
      $this->_proccessRequest($this->_prepareRequest('/user/2222', 'DELETE', true))->getStatusCode()
    );
  }

  public function testPatchName()
  {
    // existing user
    $user = $this->_findExistingUser();
    $response = $this->_proccessRequest(
      $this->_prepareRequest(
        '/user/' . $user['id'] . '/name',
        'PATCH',
        true,
        [
          'firstName' => md5(uniqid(rand(), true)),
          'lastName'  => md5(uniqid(rand(), true)),
        ]
      )
    );
    $this->assertEquals(200, $response->getStatusCode());
    $updatedUser = json_decode($response->getContent(), true);
    $this->assertNotEquals($user['firstName'], $updatedUser['firstName']);
    $this->assertNotEquals($user['lastName'], $updatedUser['lastName']);

    // non existing user
    $this->assertEquals(
      404,
      $this->_proccessRequest(
        $this->_prepareRequest(
          '/user/2222/name',
          'PATCH',
          true,
          [
            'firstName' => md5(uniqid(rand(), true)),
            'lastName'  => md5(uniqid(rand(), true)),
          ]
        )
      )->getStatusCode()
    );
  }

  public function testPatchDarkMode()
  {
    // existing user
    $user = $this->_findExistingUser();
    $response = $this->_proccessRequest($this->_prepareRequest('/user/' . $user['id'] . '/dark-mode', 'PATCH', true));
    $this->assertEquals(200, $response->getStatusCode());
    $updatedUser = json_decode($response->getContent(), true);
    $this->assertNotEquals($user['darkMode'], $updatedUser['darkMode']);

    // non existing user
    $this->assertEquals(
      404,
      $this->_proccessRequest($this->_prepareRequest('/user/2222/dark-mode', 'PATCH', true))->getStatusCode()
    );
  }

}
