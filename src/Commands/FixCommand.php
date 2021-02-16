<?php

namespace Fixer\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class FixCommand extends Command {

  private $logger;

  private $enumerator;

  private $fixer;

  private $uSleepInterval = 1000000;

  public function __construct($logger, $enumerator, $fixer, $sleepInterval) {
    parent::__construct(null);
    $this->logger = $logger;
    $this->enumerator = $enumerator;
    $this->fixer = $fixer;
    $this->uSleepInterval = $sleepInterval;
  }

  protected function execute(InputInterface $input, OutputInterface $output)
  {
    $endpoint = $input->getArgument('rest-endpoint');
    $userId = $input->getArgument('user-id');
    $token = $input->getArgument('user-token');

    $ids = $this->enumerator->getIds($endpoint, $userId, $token, $this->uSleepInterval);

    foreach ($ids as $id) {
      $this->fixer->fix($endpoint, $userId, $token, $id);
      usleep($this->uSleepInterval);
    }

        return 0;
  }
  
}