<?php

namespace UserApi\Cli;

use Cubex\Console\ConsoleCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ExampleCommand extends ConsoleCommand
{
  /**
   * @param InputInterface  $input
   * @param OutputInterface $output
   *
   * @return void
   */
  protected function executeCommand(InputInterface $input, OutputInterface $output): void
  {
    print 'Hello World!';
  }
}
