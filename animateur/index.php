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

//Page Accueil gestionnaire
$app->get('/','\\animateur\\controllers\\Controller:afficherAccueil');

//Page Connexion
$app->get('/connexion','\\animateur\\controllers\\Controller:afficherConnexion')->setName('connexion');
$app->post('/connexion','\\animateur\\controllers\\Controller:gererConnexion');
$app->get('/deconnexion','\\animateur\\controllers\\Controller:deconnexion');

// Accueil Creneau
$app->get('/creneau','\\animateur\\controllers\\Controller:voirCreneau')->setName('creneau')->add(new \animateur\middlewares\EstConnectAdmin($app->getContainer()));

// Ajouter un creneau
$app->get('/addCreneau','\\animateur\\controllers\\Controller:addCreneau')->add(new \animateur\middlewares\EstConnectAdmin($app->getContainer()))->setName('addCreneau');

// Accueil Programme
$app->get('/programme','\\animateur\\controllers\\Controller:voirProgramme')->add(new \animateur\middlewares\EstConnectAdmin($app->getContainer()))->setName('programme');

// Accueil Actualité
$app->get('/actualite','\\animateur\\controllers\\Controller:voirActualite')->add(new \animateur\middlewares\EstConnectAdmin($app->getContainer()))->setName('actualite');

// Accueil Emission
$app->get('/emission','\\animateur\\controllers\\Controller:voirEmission')->add(new \animateur\middlewares\EstConnectAdmin($app->getContainer()))->setName('emission');



//page accueil animateur

$app->get("/animateur", "\\animateur\\controllers\\AnimateurController:accueil");

/////////////
// RUN     //
/////////////
$app->run();
