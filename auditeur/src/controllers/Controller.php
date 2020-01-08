<?php

namespace auditeur\controllers;

use Illuminate\Database\Capsule\Manager as DB;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;


class Controller extends BaseController
{

    /**
     * Fonction permettant d'afficher la page d'accueil.
     * @param $request
     * @param $response
     * @return mixed
     */
    public function afficherAccueil($request, $response)
    {
        return $this->render($response, 'Accueil.html.twig');
    } //End of function afficherAccueil

    public function afficherInscription($request, $response)
    {
        return $this->render($response, 'Inscription.html.twig');
    } //End of function afficherInscription

    public function afficherConnexion($request, $response)
    {
        return $this->render($response, 'Connexion.html.twig');
    } //End of function afficherInscription


}
