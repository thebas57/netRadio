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
$app->get('/','\\animateur\\controllers\\Controller:afficherAccueil')->setName("accueil");

//Page Connexion
$app->get('/connexion','\\animateur\\controllers\\Controller:afficherConnexion')->setName('connexion');
$app->post('/connexion','\\animateur\\controllers\\Controller:gererConnexion');
$app->get('/deconnexion','\\animateur\\controllers\\Controller:deconnexion')->setName("deconnexion");

// Accueil Creneau
$app->get('/creneau','\\animateur\\controllers\\Controller:voirCreneau')->setName('creneau')->add(new \animateur\middlewares\EstConnectGestionnaire($app->getContainer()));

// Ajouter un creneau
$app->get('/addCreneau','\\animateur\\controllers\\Controller:afficherAddCreneau')->add(new \animateur\middlewares\EstConnectGestionnaire($app->getContainer()))->setName('addCreneau');
$app->post('/addCreneau','\\animateur\\controllers\\Controller:addCreneau')->add(new \animateur\middlewares\EstConnectGestionnaire($app->getContainer()));

// Supprimer un creneau
$app->get('/supprCreneau/{id}','\\animateur\\controllers\\Controller:supprCreneau')->add(new \animateur\middlewares\EstConnectGestionnaire($app->getContainer()));

// Accueil Programme
$app->get('/programme','\\animateur\\controllers\\Controller:voirProgramme')->add(new \animateur\middlewares\EstConnectGestionnaire($app->getContainer()))->setName('programme');

// Ajouter un programme
$app->get('/addProgramme','\\animateur\\controllers\\Controller:afficherAddProgramme')->add(new \animateur\middlewares\EstConnectGestionnaire($app->getContainer()))->setName('addProgramme');
$app->post('/addProgramme','\\animateur\\controllers\\Controller:addProgramme')->add(new \animateur\middlewares\EstConnectGestionnaire($app->getContainer()));

// Supprimer un pprogramme
$app->get('/supprProgramme/{id}','\\animateur\\controllers\\Controller:supprProgramme')->add(new \animateur\middlewares\EstConnectGestionnaire($app->getContainer()));

// Accueil Actualité
$app->get('/actualite','\\animateur\\controllers\\Controller:voirActualite')->add(new \animateur\middlewares\EstConnectGestionnaire($app->getContainer()))->setName('actualite');

$app->get('/ajoutStaff','\\animateur\\controllers\\Controller:afficherAjoutStaff')->add(new \animateur\middlewares\EstConnectGestionnaire($app->getContainer()));
$app->post('/ajoutStaff','\\animateur\\controllers\\Controller:ajoutStaff')->add(new \animateur\middlewares\EstConnectGestionnaire($app->getContainer()));

$app->get('/listeUsers','\\animateur\\controllers\\Controller:afficherListeUsers')->add(new \animateur\middlewares\EstConnectGestionnaire($app->getContainer()));
$app->get('/supprUser/{id}','\\animateur\\controllers\\Controller:supprUser')->add(new \animateur\middlewares\EstConnectGestionnaire($app->getContainer()));

// Accueil Emission
$app->get('/emission','\\animateur\\controllers\\Controller:voirEmission')->add(new \animateur\middlewares\EstConnectGestionnaire($app->getContainer()))->setName('emission');



//page accueil animateur

$app->get("/animateur", "\\animateur\\controllers\\AnimateurController:accueil")->setName("AccueilAnimateur")->add(new \animateur\middlewares\EstConnectAnimateur($app->getContainer()))->setName("accueilAnimateur");
$app->get("/animateur/animerProgramme/{id}", "\\animateur\\controllers\\AnimateurController:emissionsAAnimer")->add(new \animateur\middlewares\EstConnectAnimateur($app->getContainer()));
$app->get("/animateur/animerEmission/{id}", "\\animateur\\controllers\\AnimateurController:animerEmission")->add(new \animateur\middlewares\EstConnectAnimateur($app->getContainer()));
$app->post("/animateur/importerEmission", "\\animateur\\controllers\\AnimateurController:importerEmission")->add(new \animateur\middlewares\EstConnectAnimateur($app->getContainer()));

$app->post("/emission/receiveAudio", "\\animateur\\controllers\\AnimateurController:receiveAudio")->add(new \animateur\middlewares\EstConnectAnimateur($app->getContainer()));

// Ajouter une émssion
$app->get('/addEmission','\\animateur\\controllers\\Controller:afficherAddEmission')->add(new \animateur\middlewares\EstConnectGestionnaire($app->getContainer()))->setName('addEmission');
$app->post('/addEmission','\\animateur\\controllers\\Controller:addEmission')->add(new \animateur\middlewares\EstConnectGestionnaire($app->getContainer()));

// Supprimer un emission
$app->get('/supprEmission/{id}','\\animateur\\controllers\\Controller:supprEmission')->add(new \animateur\middlewares\EstConnectGestionnaire($app->getContainer()));

// Modifier un créneau
$app->get('/modifCreneau{id}', "\\animateur\\controllers\\Controller:afficherModifCreneau")->add(new \animateur\middlewares\EstConnectGestionnaire($app->getContainer()))->setName('modifCreneau');
$app->post('/modifCreneau{id}', "\\animateur\\controllers\\Controller:modifCreneau")->add(new \animateur\middlewares\EstConnectGestionnaire($app->getContainer()));



/////////////
// RUN     //
/////////////
$app->run();
