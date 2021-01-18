<?php

namespace UserApi\Forms;

use Packaged\Form\DataHandlers\TextDataHandler;
use Packaged\Form\Form\Form;
use Packaged\Validate\Validators\RequiredValidator;

class LoginForm extends Form
{

  public TextDataHandler $username;
  public TextDataHandler $password;

  protected function _initDataHandlers()
  {
    $this->username = TextDataHandler::i()->addValidator(new RequiredValidator());
    $this->password = TextDataHandler::i()->addValidator(new RequiredValidator());
  }
}
