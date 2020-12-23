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
    $user->first_name = 'John';
    $user->last_name = 'Doe';
    $user->username = 'JD';
    $user->date_created = date('Y-m-d H:i:s', time());
    $user->dark_mode = true;
    $user->save();

    $user = new User();
    $user->first_name = 'Nikolay';
    $user->last_name = 'Brankov';
    $user->username = 'NB';
    $user->date_created = date('Y-m-d H:i:s', time());
    $user->dark_mode = false;
    $user->save();

    $user = new User();
    $user->first_name = 'George';
    $user->last_name = 'Smith';
    $user->username = 'GS';
    $user->date_created = date('Y-m-d H:i:s', time());
    $user->dark_mode = true;
    $user->save();

    $user = new User();
    $user->first_name = 'Aaron';
    $user->last_name = 'Lewis';
    $user->username = 'AL';
    $user->date_created = date('Y-m-d H:i:s', time());
    $user->dark_mode = false;
    $user->save();

    $user = new User();
    $user->first_name = 'Daniel';
    $user->last_name = 'Ding';
    $user->username = 'DD';
    $user->date_created = date('Y-m-d H:i:s', time());
    $user->dark_mode = true;
    $user->save();
  }

}