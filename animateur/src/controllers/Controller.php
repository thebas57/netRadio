<?php

namespace animateur\controllers;

use auditeur\models\Utilisateur;
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
    } //End of function addCreneau

    public function afficherConnexion($request,$response){
        return $this->render($response,'Connexion.html.twig');
    }//End of function afficherConnexion

    public function gererConnexion($request,$response){
        $login = (isset($_POST['login'])) ? $_POST['login'] : null;
        $password = (isset($_POST['password'])) ? $_POST['password'] : null;

        try {
            if (!isset($login) || !isset($password)) {
                throw new \Exception("Il manque un champ !");
            }

            $login = filter_var($login, FILTER_SANITIZE_STRING);

            $user = Utilisateur::where("identifiant",$login)->first();
            if (!isset($user)){
                throw new \Exception("Le nom d'utilisateur n'existe pas !");
            }
            if (!password_verify($password,$user->password)){
                throw new \Exception("Les mots de passe ne correspondent pas !");
            }

            if ($user->droit == 0){
                throw new \Exception("Vous n'avez pas le droit d'accéder à cette zone !");
            }
            $_SESSION['user'] = ['id' => $user->utilisateur_id, 'droit' => $user->droit];
            if ($user->droit == 1){
                return $this->redirect($response,'accueilAnimateur');
            } else {
                return $this->redirect($response,'accueil');
            }


        } catch (\Exception $e) {
            return $this->render($response,'Connexion.html.twig', [ 'erreur' =>$e->getMessage() ] );
        }
    }//End of function gererConnexion

}
