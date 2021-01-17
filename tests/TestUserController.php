<?php

use Firebase\JWT\JWT;
use Cubex\Cubex;
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

  protected function _proccessRequest(Request $request, $authenticated = false): Response
  {
    if($authenticated)
    {
      $request->headers->add(['token' => $this->_jwt]);
    }
    $context = new UserApiContext($request);
    $this->_app->getCubex()->prepareContext($context);
    return $this->_app->handle($context);
  }

  public function testNotAuthenticated()
  {
    $response = $this->_proccessRequest(Request::create('/user', 'get'));
    $this->assertEquals(403, $response->getStatusCode());
  }

  public function testAuthenticated()
  {
    // Request with token query parameter
    $response = $this->_proccessRequest(
      Request::create(
        '/user',
        'get',
        ['token' => $this->_jwt]
      )
    );

    $this->assertEquals(200, $response->getStatusCode());

    // Request with token header
    $response = $this->_proccessRequest(Request::create('/user', 'get'), true);

    $this->assertEquals(200, $response->getStatusCode());
  }

  public function testGetIndex()
  {
    // simple request
    $response = $this->_proccessRequest(Request::create('/user', 'get'), true);

    $this->assertEquals(200, $response->getStatusCode());
    $this->assertIsArray(json_decode($response->getContent(), true));

    // request with limit search parameter
    $response = $this->_proccessRequest(Request::create('/user', 'get', ['limit' => 2]), true);

    $this->assertEquals(200, $response->getStatusCode());
    $this->assertEquals(2, count(json_decode($response->getContent(), true)));

    // request with firstName search parameter
    $response = $this->_proccessRequest(Request::create('/user', 'get', ['firstName' => 'Nikolay']), true);

    $this->assertEquals(200, $response->getStatusCode());
    $this->assertEquals('Nikolay', json_decode($response->getContent(), true)[0]['firstName']);

    // request with lastName search parameter
    $response = $this->_proccessRequest(Request::create('/user', 'get', ['lastName' => 'Brankov']), true);

    $this->assertEquals(200, $response->getStatusCode());
    $this->assertEquals('Brankov', json_decode($response->getContent(), true)[0]['lastName']);

    // request with username search parameter
    $response = $this->_proccessRequest(Request::create('/user', 'get', ['username' => 'NB']), true);

    $this->assertEquals(200, $response->getStatusCode());
    $this->assertEquals('NB', json_decode($response->getContent(), true)[0]['username']);

    // request with darkMode=true
    $response = $this->_proccessRequest(Request::create('/user', 'get', ['darkMode' => 'true']), true);

    $this->assertEquals(200, $response->getStatusCode());
    $users = json_decode($response->getContent(), true);
    foreach($users as $user)
    {
      $this->assertEquals(true, $user['darkMode']);
    }

    // request with darkMode=false
    $response = $this->_proccessRequest(Request::create('/user', 'get', ['darkMode' => 'false']), true);

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
    $response = $this->_proccessRequest(
      Request::create(
        '/user',
        'post',
        [],
        [],
        [],
        [],
        json_encode(
          [
            'firstName' => 'Test',
            'lastName'  => 'User',
            'username'   => 'TestUser',
            'darkMode'  => true,
          ]
        )
      ),
      true
    );

    $this->assertEquals(200, $response->getStatusCode());

    // invalid data
    $response = $this->_proccessRequest(
      Request::create(
        '/user',
        'post',
        [],
        [],
        [],
        [],
        json_encode(
          [
            'firstName' => 'Test',
            'lastName'  => 'User',
          ]
        )
      ),
      true
    );

    $this->assertEquals(400, $response->getStatusCode());
  }

  public function testGetUser()
  {
    // existing user
    $response = $this->_proccessRequest(Request::create('/user/1', 'get'), true);

    $this->assertEquals(200, $response->getStatusCode());

    // non existing user
    $response = $this->_proccessRequest(Request::create('/user/2222', 'get'), true);

    $this->assertEquals(404, $response->getStatusCode());
  }

  public function testDeleteUser()
  {
    // existing user
    $users = json_decode($this->_proccessRequest(Request::create('/user', 'get'), true)->getContent(), true);
    $id = $users[count($users) - 1]['id'];
    $response = $this->_proccessRequest(Request::create('/user/' . $id, 'delete'), true);

    $this->assertEquals(200, $response->getStatusCode());

    // non existing user
    $response = $this->_proccessRequest(Request::create('/user/2222', 'delete'), true);

    $this->assertEquals(404, $response->getStatusCode());
  }

  public function testPatchName()
  {
    // existing user
    $users = json_decode($this->_proccessRequest(Request::create('/user', 'get'), true)->getContent(), true);
    $user = $users[count($users) - 1];

    $response = $this->_proccessRequest(
      Request::create(
        '/user/' . $user['id'] . '/name',
        'patch',
        [],
        [],
        [],
        [],
        json_encode(
          [
            'firstName' => md5(uniqid(rand(), true)),
            'lastName'  => md5(uniqid(rand(), true)),
          ]
        )
      ),
      true
    );

    $this->assertEquals(200, $response->getStatusCode());
    $updatedUser = json_decode($response->getContent(), true);
    $this->assertNotEquals($user['firstName'], $updatedUser['firstName']);
    $this->assertNotEquals($user['lastName'], $updatedUser['lastName']);

    // non existing user
    $response = $this->_proccessRequest(
      Request::create(
        '/user/' . 2222 . '/name',
        'patch',
        [],
        [],
        [],
        [],
        json_encode(
          [
            'firstName' => md5(uniqid(rand(), true)),
            'lastName'  => md5(uniqid(rand(), true)),
          ]
        )
      ),
      true
    );

    $this->assertEquals(404, $response->getStatusCode());
  }

  public function testPatchDarkMode()
  {
    // existing user
    $users = json_decode($this->_proccessRequest(Request::create('/user', 'get'), true)->getContent(), true);
    $user = $users[count($users) - 1];

    $response = $this->_proccessRequest(Request::create('/user/' . $user['id'] . '/dark-mode', 'patch'), true);
    $this->assertEquals(200, $response->getStatusCode());
    $updatedUser = json_decode($response->getContent(), true);
    $this->assertNotEquals($user['darkMode'], $updatedUser['darkMode']);

    // non existing user
    $response = $this->_proccessRequest(Request::create('/user/2222/dark-mode', 'patch'), true);
    $this->assertEquals(404, $response->getStatusCode());
  }

}