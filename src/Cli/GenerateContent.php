<?php

namespace UserApi\Cli;

use Cubex\Console\ConsoleCommand;
use UserApi\Models\User;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class GenerateContent extends ConsoleCommand
{

  protected function executeCommand(InputInterface $input, OutputInterface $output)
  {
    $user = new User();
    $user->firstName = 'John';
    $user->lastName = 'Doe';
    $user->username = 'JD';
    $user->dateCreated = date('Y-m-d H:i:s', time());
    $user->darkMode = true;
    $user->save();

    $user = new User();
    $user->firstName = 'Nikolay';
    $user->lastName = 'Brankov';
    $user->username = 'NB';
    $user->dateCreated = date('Y-m-d H:i:s', time());
    $user->darkMode = false;
    $user->save();

    $user = new User();
    $user->firstName = 'George';
    $user->lastName = 'Smith';
    $user->username = 'GS';
    $user->dateCreated = date('Y-m-d H:i:s', time());
    $user->darkMode = true;
    $user->save();

    $user = new User();
    $user->firstName = 'Aaron';
    $user->lastName = 'Lewis';
    $user->username = 'AL';
    $user->dateCreated = date('Y-m-d H:i:s', time());
    $user->darkMode = false;
    $user->save();

    $user = new User();
    $user->firstName = 'Daniel';
    $user->lastName = 'Ding';
    $user->username = 'DD';
    $user->dateCreated = date('Y-m-d H:i:s', time());
    $user->darkMode = true;
    $user->save();
  }

}