<?php #bin/generate_model.php

$app = require dirname(__DIR__).'/src/bootstrap.php';

$scan = new Pomm\Tools\ScanSchemaTool(array(
    'schema' => 'gobai',
    'database' => $app['pomm']->getDatabase(),
    'prefix_dir' => PROJECT_DIR.'/src',
    ));
$scan->execute();
