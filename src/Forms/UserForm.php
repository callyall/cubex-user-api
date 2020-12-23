<?php

namespace UserApi\Forms;

use Packaged\Form\DataHandlers\TextDataHandler;
use Packaged\Form\Form\Form;
use Packaged\Validate\Validators\BoolValidator;
use Packaged\Validate\Validators\RegexValidator;
use Packaged\Validate\Validators\RequiredValidator;

class UserForm extends Form
{

  public TextDataHandler $first_name;
  public TextDataHandler $last_name;
  public TextDataHandler $username;
  public TextDataHandler $dark_mode;

  protected function _initDataHandlers()
  {
    $this->first_name = TextDataHandler::i()->addValidator(new RequiredValidator())->addValidator(
      new RegexValidator('/^.{1,50}$/')
    );
    $this->last_name = TextDataHandler::i()->addValidator(new RequiredValidator())->addValidator(
      new RegexValidator('/^.{1,50}$/')
    );
    $this->username = TextDataHandler::i()->addValidator(new RequiredValidator())->addValidator(
      new RegexValidator('/^.{6,20}$/')
    );
    $this->dark_mode = TextDataHandler::i()->addValidator(new BoolValidator());
  }
}