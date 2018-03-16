<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SetupCommand extends Command
{
    protected function configure()
    {
        $this->setName('setup');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $dbh = new \PDO('mysql:host=127.0.0.1','root','');
        $dbh->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        $output->writeln('root@127.0.0.1 Query execute: CREATE DATABASE conv_sample');
        $dbh->exec('DROP DATABASE IF EXISTS conv_sample');
        $dbh->exec('CREATE DATABASE conv_sample');
    }
}
