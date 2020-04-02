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



    public function afficherCompte($request, $response, $args)
    {

        $user = Utilisateur::find($args['id']);
        return $this->render($response, 'MonCompte.html.twig', ['utilisateur' => $user]);
    } //End of function afficherConnexion

    public function afficherComptePass($request, $response, $args)
    {

        $user = Utilisateur::find($args['id']);
        return $this->render($response, 'MonComptePass.html.twig', ['utilisateur' => $user]);
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
    } //End of function gererInscription

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
    } //End of function gererConnexion

    public function updateMdp($request, $response, $args)
    {
        //recuperation des donnees du post
        $password1 = (isset($_POST['newPass1'])) ? $_POST['newPass1'] : null;
        $password2 = (isset($_POST['newPass2'])) ? $_POST['newPass2'] : null;

        try {
            if (!isset($password1) || !isset($password2)) {
                throw new \Exception("Tous les champs doivent être complétés");
            }
            if ($password1 != $password2) {
                throw new \Exception("Les mots de passe ne correspondent pas");
            }

            $user = Utilisateur::find($args['id']);
            
            $password1 = password_hash($password1, PASSWORD_DEFAULT);
            $user->identifiant = $user->identifiant;
            $user->password = $password1;
            $user->email = $user->email;
            $user->droit = 0;

            $user->save();
            unset($password1);
            unset($password2);


            return $this->redirect($response, 'Accueil');
        } catch (\Exception $e) {
            return $this->render($response, 'MonComptePass.html.twig', ['erreur' => $e->getMessage()]);
        }
    } //End of function updateMdP

    public function deconnexion($request, $response)
    {
        if (isset($_SESSION['user'])) {
            unset($_SESSION['user']);
        }

        return $this->redirect($response, 'Accueil');
    } //End of function deconnexion


    public function ecouterDirect($request, $response)
    {
        date_default_timezone_set('Europe/Paris');

        $creneauMtn = null;
        $emissionMtn = null;
        $creneauAvenir = [];
        $emissionsAvenir = [];
        $creneauPasse = [];
        $emissionsPassees = [];
        $hActuelle = date("H:i:s");
        $dateActuelle = date("Y-m-d");
        $tempsAvantEmis = "";
        $heureProchaineEmission = "";
        $titreProchaineEmission = "";
        $idProchaineEmission = "";

        $creneaux = Creneau::where('date_creneau', $dateActuelle)->orderBy('heure_debut')->get();
        $emissions = Emission::all();

        // Parcourir tous les créneaux pour obtenir celui actuel
        foreach ($creneaux as $creneau) {
            if ($creneau->heure_debut < $hActuelle && $hActuelle < $creneau->heure_fin) {
                $creneauMtn = $creneau;
            } else if ($creneau->heure_debut > $hActuelle) {
                array_push($creneauAvenir, $creneau);
            } else {
                array_push($creneauPasse, $creneau);
            }
        }

        // Parcours des émissions afin d'obtenir le nom, description... de l'émission sur le créneau actuel, à venir, passé
        foreach($emissions as $emission) {
            // Pour obtention des propriétés de l'émission au créneau actuel
            if($creneauMtn != null) {
                if($creneauMtn->emission_id == $emission->emission_id) {
                    $emissionMtn=$emission;
                }
            }
            if(!empty($creneauAvenir)) {
                // Pour obtention des propriétés des émissions à venir
                foreach ($creneauAvenir as $prochainCreneau) {
                    if($prochainCreneau->emission_id == $emission->emission_id) {
                        array_push($emissionsAvenir, $emission);
                    }
                }
            }
            if(!empty($creneauPasse)) {
                // Pour obtention des propriétés des émissions passées
                foreach ($creneauPasse as $creneauxPass) {
                    if($creneauxPass->emission_id == $emission->emission_id) {
                        array_push($emissionsPassees, $emission);
                    }
                }
            }
        }

        if (!empty($creneauAvenir)){
            $heureProchaineEmission = $creneauAvenir[0]->heure_debut;
            $h1 = strtotime($creneauAvenir[0]->heure_debut);
            $h2 = strtotime($hActuelle);
            $tempsAvantEmis = gmdate("H:i", $h1 - $h2);
            $idProchaineEmission = $creneauAvenir[0]->emission_id;
            foreach ($emissions as $uneEmission) {
                if($uneEmission->emission_id == $idProchaineEmission) {
                    
                    $titreProchaineEmission = $uneEmission->titre;
                }
            }
        }

        return $this->render($response, 'Direct.html.twig', ['emissionMtn' => $emissionMtn, 'emissionsAvenir' => $emissionsAvenir, 'emissionsPassees' => $emissionsPassees, 'creneaux' => $creneaux, 'creneauMtn' => $creneauMtn, 'creneauAvenir' => $creneauAvenir, 'creneauPasse' => $creneauPasse, 'tempsAvantEmis' => $tempsAvantEmis, 'heureProchEmiss' => $heureProchaineEmission, 'titreProchaineEmis' => $titreProchaineEmission]);
    } //End of function ecouterDirect


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
}
