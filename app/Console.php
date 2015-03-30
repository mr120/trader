<?php

use Symfony\Component\Console\Application as ConsoleApplication;
use Silex\Application as SilexApplication;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Helper\HelperSet;
use Doctrine\DBAL\Tools\Console\Helper\ConnectionHelper;
use Doctrine\ORM\Tools\Console\Helper\EntityManagerHelper;
use Doctrine\ORM\Tools\Console\ConsoleRunner;

require_once('App.php');

$app = new App();

$console = new ConsoleApplication('Silex - Rest API Edition', '1.0');
$console->getDefinition()->addOption(new InputOption('--env','-e', InputOption::VALUE_REQUIRED, 'The environment name', 'dev'));

/*
 * Doctrine CLI
 */
$helperSet = new HelperSet(array(
    'db' => new ConnectionHelper($app['db']),
    'em' => new EntityManagerHelper($app['orm.em'])
));


$console->setHelperSet($helperSet);
ConsoleRunner::addCommands($console);

$console->run();