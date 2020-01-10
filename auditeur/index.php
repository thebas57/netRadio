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
    $view->addExtension(new \Twig\Extension\DebugExtension());
    $view->getEnvironment()->addGlobal('session',$_SESSION);
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

$app->get('/','\\auditeur\\controllers\\Controller:afficherAccueil')->setName("Accueil");
$app->get('/inscription','\\auditeur\\controllers\\Controller:afficherInscription');
$app->get('/connexion','\\auditeur\\controllers\\Controller:afficherConnexion');
$app->get('/monCompte/{id}','\\auditeur\\controllers\\Controller:afficherCompte');

$app->get('/deconnexion','\\auditeur\\controllers\\Controller:deconnexion');

$app->post('/inscription','\\auditeur\\controllers\\Controller:gererInscription');
$app->post('/connexion','\\auditeur\\controllers\\Controller:gererConnexion');
$app->post('/monCompte/{id}','\\auditeur\\controllers\\Controller:updateLogin');





/////////////
// RUN     //
/////////////
$app->run();
