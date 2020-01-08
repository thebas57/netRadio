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
    } //End of function afficherConnexion

    public function gererInscription($request,$response){
        //recuperation des donnees du post
        $email = (isset($_POST['mail'])) ? $_POST['mail'] : null;
        $login = (isset($_POST['login'])) ? $_POST['login'] : null;
        $password = (isset($_POST['password'])) ? $_POST['password'] : null;

        try {
            if (!isset($email) || !isset($password) || !isset($login)){
                throw new \Exception("Il manque une donnée");
            }
            if (!empty(Utilisateur::where("identifiant",$login)->first())){
                throw new \Exception("Le nom d'utilisateur est déjà pris !");
            }
            $email = filter_var($email, FILTER_SANITIZE_EMAIL);
            $login = filter_var($login, FILTER_SANITIZE_STRING);

            $password = password_hash($password,PASSWORD_DEFAULT);
            $user = new Utilisateur();
            $user->login = $login;
            $user->password = $password;
            $user->email = $email;

            unset($login);
            unset($password);
            unset($email);
        } catch (\Exception $e){
            return $this->render($response,'Inscription.html.twig', [ erreur =>$e->getMessage() ] );
        }
    }//End of function gererInscription

}
