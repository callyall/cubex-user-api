<?php

namespace UserApi\Context;

use Cubex\Context\Context;
use Cubex\Context\Events\ConsoleCreatedEvent;
use Cubex\Cubex;
use Exception;
use Firebase\JWT\JWT;
use Packaged\Validate\ValidationException;
use UserApi\Cli\Application;

class UserApiContext extends Context
{

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

  /**
   * @return bool
   */
  public function isAuthenticated(): bool
  {
    $token = $this->request()->query->has('token') ? $this->request()->query->get('token') :
      $this->request()->headers->get('token');

    if($token)
    {
      try
      {
        JWT::decode($token, $this->getConfig()->getSection('jwt')->getItem('key'), ['HS256']);
        return true;
      }
      catch(Exception $e)
      {
      }
    }

    return false;
  }

  public function getErrorMessages(array $fields): array
  {
    foreach($fields as $name => $validationErrors)
    {
      /**
       * @var ValidationException $error
       */
      foreach($validationErrors as $key => $error)
      {
        $fields[$name][$key] = $error->getMessage();
      }
    }
    return $fields;
  }

}
