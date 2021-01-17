<?php

namespace UserApi\Forms;

use Packaged\Form\DataHandlers\TextDataHandler;
use Packaged\Form\Form\Form;
use Packaged\Validate\Validators\BoolValidator;
use Packaged\Validate\Validators\RegexValidator;
use Packaged\Validate\Validators\RequiredValidator;

class UserForm extends Form
{

  public TextDataHandler $firstName;
  public TextDataHandler $lastName;
  public TextDataHandler $username;
  public TextDataHandler $darkMode;

  protected function _initDataHandlers()
  {
    $this->firstName = TextDataHandler::i()->addValidator(new RequiredValidator())->addValidator(
      new RegexValidator('/^.{1,50}$/')
    );
    $this->lastName = TextDataHandler::i()->addValidator(new RequiredValidator())->addValidator(
      new RegexValidator('/^.{1,50}$/')
    );
    $this->username = TextDataHandler::i()->addValidator(new RequiredValidator())->addValidator(
      new RegexValidator('/^.{6,20}$/')
    );
    $this->darkMode = TextDataHandler::i()->addValidator(new BoolValidator());
  }
}