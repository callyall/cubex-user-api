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
    $user->date_created = date("Y-m-d H:i:s", time());
    $user->dark_mode = true;
    $user->save();
  }

}