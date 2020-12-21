<?php

namespace UserApi\Models;

use \Packaged\Dal\Ql\QlDao;

class User extends QlDao
{
  protected $_dataStoreName = 'user_api';

  public int $id = 0;
  public string $first_name = '';
  public string $last_name = '';
  public string $username = '';
  public string $date_created = '';
  public bool $dark_mode = false;
}