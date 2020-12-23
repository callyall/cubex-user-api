<?php

namespace UserApi\Context;

use Cubex\Context\Context;
use Cubex\Context\Events\ConsoleCreatedEvent;
use Cubex\Cubex;
use Firebase\JWT\JWT;
use UserApi\Cli\Application;

class UserApiContext extends Context
{

  protected bool $_isAuthenticated = false;

  protected function _construct()
  {
    parent::_construct();
    //Setup Database connections for Console Commands
    $this->events()->listen(
      ConsoleCreatedEvent::class,
      function () {
        $this->getCubex()->share(Application::class, Application::launch($this), Cubex::MODE_IMMUTABLE);
      }
    );

  }

  protected function _initialize()
  {
    $token = $this->request()->query->has('token') ? $this->request()->query->get('token') :
      $this->request()->headers->get('token');

    if($token)
    {
      try
      {
        JWT::decode($token, $this->getConfig()->getSection('jwt')->getItem('key'), ['HS256']);
        $this->_isAuthenticated = true;
      }
      catch(\Exception $e)
      {}
    }
  }

  /**
   * @return bool
   */
  public function isAuthenticated(): bool
  {
    return $this->_isAuthenticated;
  }

}