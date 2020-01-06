<?php
$ROOT = __DIR__ ."/../../";
$SRC = $ROOT . 'src/';
$PUBLIC = $ROOT . 'public/';
global $ROOT;
global $SRC;
global $PUBLIC;
$config=array();

$config['displayErrorDetails']=true;
$config['addContentLengthHeader']=false;

$config['determineRouteBeforeAppMiddleware'] = true;

$config['db']= [
    'driver' => 'mysql',
    'host' => 'localhost',
    'database' => '',
    'username' => '',
    'password' => '',
    'charset' => 'utf8',
    'collation' => 'utf8_general_ci'
];
