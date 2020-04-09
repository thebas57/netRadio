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

    /**
     * @param $request
     * @param $response
     * @return mixed
     * Affiche la page d'inscription
     */
    public function afficherInscription($request, $response)
    {
        return $this->render($response, 'Inscription.html.twig');
    } //End of function afficherInscription

    /**
     * @param $request
     * @param $response
     * @return mixed
     * Affiche la page de connexion
     */
    public function afficherConnexion($request, $response)
    {
        return $this->render($response, 'Connexion.html.twig');
    } //End of function afficherConnexion

    /**
     * @param $request
     * @param $response
     * @return mixed
     * Affiche la page avec tous les programmes
     */
    public function afficherProgrammes($request, $response)
    {

        $programmes = Programme::where("deleted_at",null)->get();


    /**
     * @param $request
     * @param $response
     * @param $args
     * @return mixed
     * Permet d'afficher les émissions selon le programme choisi
     */
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

    /**
     * @param $request
     * @param $response
     * @param $args
     * @return mixed
     * Affiche le compte de l'utilisateur
     */
    public function afficherCompte($request, $response, $args)
    {

        $user = Utilisateur::find($args['id']);
        return $this->render($response, 'MonCompte.html.twig', ['utilisateur' => $user]);
    } //End of function afficherConnexion


    /**
     * @param $request
     * @param $response
     * @return ResponseInterface
     * Gère l'inscription de l'utilisateur
     */
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

    /**
     * @param $request
     * @param $response
     * @return ResponseInterface
     * Gère la connexion après envoi du formulaire
     */
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

    /**
     * @param $request
     * @param $response
     * @return ResponseInterface
     * Déconnexion de l'utilisateur
     */
    public function deconnexion($request, $response)
    {
        if (isset($_SESSION['user'])) {
            unset($_SESSION['user']);
        }

        return $this->redirect($response, 'Accueil');
    }//End of function deconnexion
    

    /**
     * @param $request
     * @param $response
     * @param $args
     * Permet de modifier le login de l'utilisateur
     */
        public function updateLogin($request, $response, $args)
    {

        $newlog = $_POST['login'];
        $postId = intval($_POST['id']);
        // $existLogin = Utilisateur::where('identifiant', 'like',$newlog)
        //   ->first();



        // test si le login existe
        $login = Utilisateur::find($postId);

        // return json_encode($_POST['id']);

        //   if($login->identifiant != $_POST['newLogin'])
        //   {
        //     if ($existLogin)
        //     {
        //         echo "<script>alert(\"icciiii\")</script>";
        //     }
        //   }

        //   echo "<script>alert(\"laaaaa\")</script>";


        $login->identifiant   = $newlog;

        $login->save();
        return json_encode($login);
    }

        public function ecouterDirect($request, $response)
    {
        date_default_timezone_set('Europe/Paris');

        $creneauMtn = null;
        $creneauAvenir = [];
        $creneauPasse = [];
        $hActuelle = date("H:i:s");
        $dateActuelle = date("Y-m-d");
        $hAvenir = "";

        $creneaux = Creneau::where('date_creneau', $dateActuelle)->orderBy('heure_debut')->get();

        foreach ($creneaux as $creneau) {
            if ($creneau->heure_debut < $hActuelle && $hActuelle < $creneau->heure_fin) {
                $creneauMtn = $creneau;
            } else if ($creneau->heure_debut > $hActuelle) {
                array_push($creneauAvenir, $creneau);
            } else {
                array_push($creneauPasse, $creneau);
            }
        }

        if (empty($creneauAvenir)) {
        } else {
            $hAvenir = $creneauAvenir[0]->heure_debut;
            $h1 = strtotime($creneauAvenir[0]->heure_debut);
            $h2 = strtotime($hActuelle);
            $hAvenir = gmdate("H:i", $h1 - $h2);
        }

        return $this->render($response, 'Direct.html.twig', ['creneaux' => $creneaux, 'creneauMtn' => $creneauMtn, 'creneauAvenir' => $creneauAvenir, 'creneauPasse' => $creneauPasse, 'hAvenir' => $hAvenir]);
    } //End of function ecouterDirect
}

