<?php

namespace UserApi\Models;

use \Packaged\Dal\Ql\QlDao;

class User extends QlDao
{
  protected string $_dataStoreName = 'user_api';

  public int $id = 0;
  public string $first_name = '';
  public string $last_name = '';
  public string $username = '';
  public string $date_created = '';
  public bool $dark_mode = false;

  public function __toString(): string
  {
    return json_encode(
      [
        'id' => $this->id,
        'first_name' => $this->first_name,
        'last_name' => $this->last_name,
        'username' => $this->username,
        'date_created' => $this->date_created,
        'dark_mode' => $this->dark_mode
      ]
    );
  }

}