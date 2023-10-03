<?php

namespace UserApi\Tests\Mock;

use Packaged\Http\Request;
use UserApi\Context\UserApiContext;

class UserApiContextMock extends UserApiContext
{
  // We need this, otherwise unit tests fail
  protected function _construct() {}

  protected ?Request $request = null;
  public function request()
  {
    return $this->request;
  }

  /**
   * @param Request|null $request
   *
   * @return UserApiContextMock
   */
  public function setRequest(?Request $request): UserApiContextMock
  {
    $this->request = $request;
    return $this;
  }
}
