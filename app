#!/usr/bin/env php
<?php
require dirname(__FILE__).'/vendor/autoload.php';
$app = new \Symfony\Component\Console\Application();
$app->add(new \App\Command\SetupCommand());
$app->add(new \App\Command\GenerateCommand());
$app->run();
