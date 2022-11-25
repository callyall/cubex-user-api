<?php

namespace UserApi\Models;

use Packaged\Dal\Ql\QlDao;

class User extends QlDao
{
  protected $_dataStoreName = 'userApi';

  public int $id = 0;
  public string $firstName = '';
  public string $lastName = '';
  public string $username = '';
  public string $dateCreated = '';
  public bool $darkMode = false;

  public function __toString(): string
  {
    return json_encode(
      [
        'id'          => $this->id,
        'firstName'   => $this->firstName,
        'lastName'    => $this->lastName,
        'username'    => $this->username,
        'dateCreated' => $this->dateCreated,
        'darkMode'    => $this->darkMode,
      ]
    );
  }

}
