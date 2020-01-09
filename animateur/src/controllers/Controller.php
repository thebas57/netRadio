<?php

namespace animateur\controllers;

use animateur\models\Programme;
use animateur\models\Emission;
use animateur\models\Utilisateur;

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
        $prog = Programme::all();
        return $this->render($response, 'Programme.html.twig',['programmes'=>$prog]);
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
    public function voirEmission($request, $response,$args)
    {
        $emission = Emission::all();
        /*
        $anim = Utilisateur::first()
            ->leftJoin('utilisateur', 'utilisateur.utilisateur_id', '=', 'emission.animateur')
            ->get();
            */
        return $this->render($response, 'Emission.html.twig',['emissions'=>$emission]);
    } //End of function voirEmission

    /**
     * Fonction permettant d'ajouter des creneau.
     * @param $request
     * @param $response
     * @return mixed
     */
    public function addCreneau($request, $response)
    {
        return $this->render($response, 'AddCreneau.html.twig');
    } //End of function addProgramme

    /**
     * Fonction permettant d'ajouter des programmes.
     * @param $request
     * @param $response
     * @return mixed
     */
    public function afficherAddProgramme($request, $response)
    {
        return $this->render($response, 'AddProgramme.html.twig');
    } //End of function addProgramme

    /**
     * Fonction permettant l'ajout d'un programme en BDD
     * @param $request
     * @param $response
     * @return mixed
     */
    public function addProgramme($request, $response)
    {
        try {
            //on récupère les données du formulaire
            $nom = (!empty($_POST['nom'])) ? $_POST['nom'] : null;
            $desc = (!empty($_POST['desc'])) ? $_POST['desc'] : null;

            //on verifie que les champs sont tous remplis
            if (!isset($nom) || !isset($desc))
                throw new \Exception("un champs requis n'a pas été rempli");

            //on filtre les données
            $nom = filter_var($nom, FILTER_SANITIZE_STRING);
            $desc = filter_var($desc, FILTER_SANITIZE_STRING);

            //on les insère en bdd
            $prog = new Programme();
            $prog->nom = $nom;
            $prog->description = $desc;
            $prog->save();

            //libération des variables
            unset($nom);
            unset($desc);

            //redirection
            $prog = Programme::all();
            return $this->redirect($response, 'programme');
        } catch (\Exception $e) {
            die($e->getMessage());
        }
    } //end of function addProgramme

    /**
     * Fonction permettant d'afficher l'ajout des emissions.
     * @param $request
     * @param $response
     * @return mixed
     */
    public function afficherAddEmission($request, $response,$args)
    {
        $prog = Programme::all();
        $anim = Utilisateur::all();
        return $this->render($response, 'AddEmission.html.twig', ['programmes'=>$prog,'utilisateurs'=>$anim]);
    } //End of function addEmission

    /**
     * Fonction permettant l'ajout d'une emssion en BDD
     * @param $request
     * @param $response
     * @return mixed
     */
    public function addEmission($request, $response)
    {
        try {
            //on récupère les données du formulaire
            $titre = (!empty($_POST['titre'])) ? $_POST['titre'] : null;
            $resume = (!empty($_POST['resume'])) ? $_POST['resume'] : null;
            $fichier = (!empty($_POST['fichier'])) ? $_POST['fichier'] : null;
            $animateur = (!empty($_POST['animateur'])) ? $_POST['animateur'] : null;
            $programme = (!empty($_POST['programme'])) ? $_POST['programme'] : null;

            //on verifie que les champs sont tous remplis
            if (!isset($titre) || !isset($resume) || !isset($animateur) || !isset($programme))
                throw new \Exception("un champs requis n'a pas été rempli");

            //on filtre les données
            $titre = filter_var($titre, FILTER_SANITIZE_STRING);
            $resume = filter_var($resume, FILTER_SANITIZE_STRING);
            $animateur = filter_var($animateur, FILTER_SANITIZE_NUMBER_INT);
            $programme = filter_var($programme, FILTER_SANITIZE_NUMBER_INT);

            //on les insère en bdd
            $emission = new Emission();
            $emission->titre = $titre;
            $emission->resume = $resume;
            $emission->fichier = $fichier;
            $emission->animateur = $animateur;
            $emission->programme_id = $programme;
            $emission->save();

            //libération des variables
            unset($titre);
            unset($resume);
            unset($fichier);
            unset($animateur);
            unset($programme);

            //redirection
            $emission = Emission::all();
            return $this->redirect($response, 'emission');
        } catch (\Exception $e) {
            die($e->getMessage());
        }
    } //end of function addEmission
}
