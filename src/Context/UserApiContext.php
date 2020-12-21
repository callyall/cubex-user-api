<?php

namespace UserApi\Context;

use Cubex\Context\Context;
use Cubex\Context\Events\ConsoleCreatedEvent;
use Cubex\Cubex;
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

}