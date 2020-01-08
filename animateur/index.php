<?php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

require __DIR__ . '/src/config/config.inc.php';
require __DIR__ . '/vendor/autoload.php';

// Create container
$container = array();


///////////
// TWIG //
//////////

// Register component on container
$container['view'] = function ($container) {
    $view = new \Slim\Views\Twig('src/views', ["debug" => true]);

    // Instantiate and add Slim specific extension
    $router = $container->get('router');
    $uri = \Slim\Http\Uri::createFromEnvironment(new \Slim\Http\Environment($_SERVER));
    $view->addExtension(new \Slim\Views\TwigExtension($router, $uri));

  //  $view->addExtension(new \Twig_Extension_Debug());

    return $view;
};


///////////////
// ELOQUENT //
//////////////
$container['settings'] = $config;
$capsule = new \Illuminate\Database\Capsule\Manager;
$capsule->addConnection($container['settings']['db']);
$capsule->setAsGlobal();
$capsule->bootEloquent();
$container['db'] = function ($container) use ($capsule) {
    return $capsule;
};

// Init Slim
$app = new \Slim\App($container);

//session_start
session_start();


//////////////
// ROUTAGE //
/////////////

//Page Accueil
$app->get('/','\\animateur\\controllers\\Controller:afficherAccueil');

// Accueil Creneau
$app->get('/creneau','\\animateur\\controllers\\Controller:voirCreneau')->setName('creneau');

// Accueil Programme
$app->get('/programme','\\animateur\\controllers\\Controller:voirProgramme')->setName('programme');

// Accueil Actualité
$app->get('/actualite','\\animateur\\controllers\\Controller:voirActualite')->setName('actualite');

// Accueil Emission
$app->get('/emission','\\animateur\\controllers\\Controller:voirEmission')->setName('emission');

/////////////
// RUN     //
/////////////
$app->run();
