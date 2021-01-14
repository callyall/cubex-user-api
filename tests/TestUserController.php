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

    // request with first_name search parameter
    $response = $this->_proccessRequest(Request::create('/user', 'get', ['first_name' => 'Nikolay']), true);

    $this->assertEquals(200, $response->getStatusCode());
    $this->assertEquals('Nikolay', json_decode($response->getContent(), true)[0]['first_name']);

    // request with last_name search parameter
    $response = $this->_proccessRequest(Request::create('/user', 'get', ['last_name' => 'Brankov']), true);

    $this->assertEquals(200, $response->getStatusCode());
    $this->assertEquals('Brankov', json_decode($response->getContent(), true)[0]['last_name']);

    // request with username search parameter
    $response = $this->_proccessRequest(Request::create('/user', 'get', ['username' => 'NB']), true);

    $this->assertEquals(200, $response->getStatusCode());
    $this->assertEquals('NB', json_decode($response->getContent(), true)[0]['username']);

    // request with dark_mode=true
    $response = $this->_proccessRequest(Request::create('/user', 'get', ['dark_mode' => 'true']), true);

    $this->assertEquals(200, $response->getStatusCode());
    $users = json_decode($response->getContent(), true);
    foreach($users as $user)
    {
      $this->assertEquals(true, $user['dark_mode']);
    }

    // request with dark_mode=false
    $response = $this->_proccessRequest(Request::create('/user', 'get', ['dark_mode' => 'false']), true);

    $this->assertEquals(200, $response->getStatusCode());
    $users = json_decode($response->getContent(), true);
    foreach($users as $user)
    {
      $this->assertEquals(false, $user['dark_mode']);
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
            'first_name' => 'Test',
            'last_name'  => 'User',
            'username'   => 'TestUser',
            'dark_mode'  => true,
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
            'first_name' => 'Test',
            'last_name'  => 'User',
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
            'first_name' => md5(uniqid(rand(), true)),
            'last_name'  => md5(uniqid(rand(), true)),
          ]
        )
      ),
      true
    );

    $this->assertEquals(200, $response->getStatusCode());
    $updatedUser = json_decode($response->getContent(), true);
    $this->assertNotEquals($user['first_name'], $updatedUser['first_name']);
    $this->assertNotEquals($user['last_name'], $updatedUser['last_name']);

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
            'first_name' => md5(uniqid(rand(), true)),
            'last_name'  => md5(uniqid(rand(), true)),
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
    $this->assertNotEquals($user['dark_mode'], $updatedUser['dark_mode']);

    // non existing user
    $response = $this->_proccessRequest(Request::create('/user/2222/dark-mode', 'patch'), true);
    $this->assertEquals(404, $response->getStatusCode());
  }

}