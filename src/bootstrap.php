<?php #sources/bootstrap.php

define('PROJECT_DIR', dirname(__DIR__));

require PROJECT_DIR.'/conf/environment.php';

// autoloader
$loader = require PROJECT_DIR.'/vendor/autoload.php';
$loader->add('Marketplace', PROJECT_DIR.'/src');

$app = new Silex\Application();

// configuration parameters
if (!file_exists(PROJECT_DIR.'/conf/config.php')) {
    throw new \RunTimeException("No config.php found in config.");
}

require PROJECT_DIR.'/conf/config.php';

// extensions registration

use Silex\Provider;

$app->register(new Provider\UrlGeneratorServiceProvider());
$app->register(new Provider\SessionServiceProvider());
$app->register(new Provider\TwigServiceProvider(), array(
    'twig.path' => array(PROJECT_DIR.'/sources/templates'),
));
$app->register(new GHub\Silex\Pomm\PommServiceProvider(), array(
    'pomm.databases' => $app['config.db'][ENV]
));

// Service container customization. 
$app['loader'] = $loader;
$app['pomm.connection'] = $app['pomm']->getDatabase()->createConnection();

// set DEBUG mode or not
if (preg_match('/^dev/', ENV))
{
    require PROJECT_DIR.'/src/GhLogger.php';
    require PROJECT_DIR.'/src/GhLoggerFilter.php';

    $app['debug'] = true;
    $app['logger'] = new GhLogger(PROJECT_DIR.'/logs/silex.log');
    $app['pomm.connection']->registerFilter(new GhLoggerFilter($app['logger']));
}

return $app;
