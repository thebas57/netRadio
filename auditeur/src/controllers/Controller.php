<?php

namespace auditeur\controllers;

use Illuminate\Database\Capsule\Manager as DB;
use mysql_xdevapi\Exception;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use auditeur\models\Utilisateur;
use auditeur\models\Emission;
use auditeur\models\Favoris;
use auditeur\models\Programme;
use auditeur\models\Creneau;


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

    public function afficherProgrammes($request, $response)
    {

        $programmes = Programme::where("deleted_at",null)->get();

        return $this->render($response, 'Programme.html.twig', ["programmes" => $programmes]);
    }//End of function afficherConnexion

    public function afficherEmissions($request, $response, $args){
        $id = intval($args['id']);

        $emissions = DB::table("EMISSION")
            ->leftJoin('CRENEAU','CRENEAU.creneau_id','=','EMISSION.emission_id')
            ->where("EMISSION.programme_id","=",$id)
            ->where("EMISSION.deleted_at",null)
            ->where("CRENEAU.deleted_at",null)
            ->get();

        return $this->render($response, 'Emissions.html.twig', ['emissions' => $emissions]);

    }//End of function afficherEmissions

    public function afficherCompte($request, $response, $args)
    {

        $user = Utilisateur::find($args['id']);
        return $this->render($response, 'MonCompte.html.twig', ['utilisateur' => $user]);
    } //End of function afficherConnexion


    public function gererInscription($request, $response)
    {
        //recuperation des donnees du post
        $email = (isset($_POST['email'])) ? $_POST['email'] : null;
        $login = (isset($_POST['login'])) ? $_POST['login'] : null;
        $password = (isset($_POST['password'])) ? $_POST['password'] : null;

        try {
            if (!isset($email) || !isset($password) || !isset($login)) {
                throw new \Exception("Il manque une donnée");
            }
            if (!empty(Utilisateur::where("identifiant", $login)->orWhere("email", $email)->first())) {
                throw new \Exception("Un compte existe déjà avec ces informations !");
            }
            $email = filter_var($email, FILTER_SANITIZE_EMAIL);
            $login = filter_var($login, FILTER_SANITIZE_STRING);

            $password = password_hash($password, PASSWORD_DEFAULT);
            $user = new Utilisateur();
            $user->identifiant = $login;
            $user->password = $password;
            $user->email = $email;
            $user->droit = 0;

            $user->save();
            unset($login);
            unset($password);
            unset($email);
            unset($user);

            return $this->redirect($response, 'Accueil');
        } catch (\Exception $e) {
            return $this->render($response, 'Inscription.html.twig', ['erreur' => $e->getMessage()]);
        }
    }//End of function gererInscription

    public function gererConnexion($request, $response)
    {
        $login = (isset($_POST['login'])) ? $_POST['login'] : null;
        $password = (isset($_POST['password'])) ? $_POST['password'] : null;

        try {
            if (!isset($login) || !isset($password)) {
                throw new \Exception("Il manque un champ !");
            }

            $login = filter_var($login, FILTER_SANITIZE_STRING);

            $user = Utilisateur::where("identifiant", $login)->first();
            if (!isset($user)) {
                throw new \Exception("Le nom d'utilisateur n'existe pas !");
            }
            if (!password_verify($password, $user->password)) {
                throw new \Exception("Les mots de passe ne correspondent pas !");
            }

            $_SESSION['user'] = ['id' => $user->utilisateur_id, 'droit' => $user->droit];

            return $this->redirect($response, 'Accueil');

        } catch (\Exception $e) {
            return $this->render($response, 'Connexion.html.twig', ['erreur' => $e->getMessage()]);
        }
    }//End of function gererConnexion

    public function deconnexion($request, $response)
    {
        if (isset($_SESSION['user'])) {
            unset($_SESSION['user']);
        }

        return $this->redirect($response, 'Accueil');
    }//End of function deconnexion


    public function updateLogin($request, $response, $args)
    {

        $existLogin = Utilisateur::where('identifiant', 'like', $_POST['newLogin'])
            ->first();


        // test si le login existe
        $login = Utilisateur::find($args['id']);

        if ($login->identifiant != $_POST['newLogin']) {
            if ($existLogin) {

            }
        }

        $login->identifiant = $_POST['newLogin'];

        $login->save();

    }
}

