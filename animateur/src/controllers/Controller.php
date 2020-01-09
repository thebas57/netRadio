<?php

namespace animateur\controllers;

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

    /**
     * Fonction permettant d'afficher les creneaux.
     * @param $request
     * @param $response
     * @return mixed
     */
    public function voirCreneau($request, $response)
    {
        return $this->render($response, 'Creneau.html.twig');
    } //End of function voirCreaneau

    /**
     * Fonction permettant d'afficher les programmes.
     * @param $request
     * @param $response
     * @return mixed
     */
    public function voirProgramme($request, $response)
    {
        return $this->render($response, 'Programme.html.twig');
    } //End of function voirProgramme

    /**
     * Fonction permettant d'afficher les actualités.
     * @param $request
     * @param $response
     * @return mixed
     */
    public function voirActualite($request, $response)
    {
        return $this->render($response, 'Actualite.html.twig');
    } //End of function voirActualite

    /**
     * Fonction permettant d'afficher les emissions.
     * @param $request
     * @param $response
     * @return mixed
     */
    public function voirEmission($request, $response)
    {
        return $this->render($response, 'Emission.html.twig');
    } //End of function voirEmission

    /**
     * Fonction permettant d'afficher les emissions.
     * @param $request
     * @param $response
     * @return mixed
     */
    public function addCreneau($request, $response)
    {
        return $this->render($response, 'AddCreneau.html.twig');
    } //End of function voirEmission

}
