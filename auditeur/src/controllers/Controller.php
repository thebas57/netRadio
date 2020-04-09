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

        $emissionRap = Emission::where('emission_id', 1)->first();
        $programmeRap = Programme::where('programme_id', 3)->first();
        $creneauRap = Creneau::where('creneau_id', 3)->first();

        $emissionInfo = Emission::where('emission_id', 2)->first();
        $programmeInfo = Programme::where('programme_id', 2)->first();
        $creneauInfo = Creneau::where('creneau_id', 1)->first();

        $emissionCuisine = Emission::where('emission_id', 3)->first();
        $programmeCuisine = Programme::where('programme_id', 1)->first();
        $creneauCuisine = Creneau::where('creneau_id', 13)->first();

        $programmeBeauté = Programme::where('programme_id', 4)->first();

        $programmeJt = Programme::where('programme_id', 2)->first();


        return $this->render($response, 'Accueil.html.twig', ['emissionRap' => $emissionRap, 'programmeRap' => $programmeRap, 'creneauRap' => $creneauRap, 'emissionInfo' => $emissionInfo, 'programmeInfo' => $programmeInfo, 'creneauInfo' => $creneauInfo, 'emissionCuisine' => $emissionCuisine, 'programmeCuisine' => $programmeCuisine, 'creneauCuisine' => $creneauCuisine, 'programmeBeaute' => $programmeBeauté, 'programmeJt' => $programmeJt]);
    } //End of function afficherAccueil

    public function afficherEmissions($request, $response, $args)
    {
        $emissions = Emission::where('programme_id', $args['id'])->get();
        $programme = Programme::find($args['id']);

        return $this->render($response, 'Emissions.html.twig', ['emissions' => $emissions, 'programme' => $programme]);
    } //End of function afficherEmissions

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

    public function afficherDetailReplay($request, $response, $args)
    {
        
        $creneau = Creneau::find($args['id']);
        $emission = Emission::where('emission_id', $creneau->emission_id)->first();
        $programme = Programme::where('programme_id', $emission->programme_id)->first();
        $utilisateur = Utilisateur::where('utilisateur_id', $emission->animateur)->first();

        return $this->render($response, 'DetailEmissionReplay.html.twig', ['creneauChoisi' => $creneau, 'emission' => $emission, 'programme' => $programme, 'animateur' => $utilisateur]);
    } //End of function afficherDetailReplay

    public function afficherReplays($request, $response)
    {
        date_default_timezone_set('Europe/Paris');

        $creneauPasse = [];
        $emissionsPassees = [];
        $hActuelle = date("H:i:s");
        $dateActuelle = date("Y-m-d");
        $animateurs = [];
        $prog = [];
        

        $creneaux = Creneau::orderBy('heure_debut')->get();
        $emissions = Emission::all();
        $programmes = Programme::all();
        $utilisateurs = Utilisateur::all();

        // Parcourir tous les créneaux pour obtenir ceux passés
        foreach ($creneaux as $creneau) {
            if ($creneau->date_creneau < $dateActuelle) {
                array_push($creneauPasse, $creneau);
            }
        }

        // Parcours des émissions afin d'obtenir le nom, description... des émissions passées
        foreach($emissions as $emission) {
            // Si il y a des créneau passées
            if(!empty($creneauPasse)) {
                // Pour obtention des propriétés des émissions passées
                foreach ($creneauPasse as $replayCreneau) {
                    if($replayCreneau->emission_id == $emission->emission_id) {
                        // Tableau des propriétés des émissions passées
                        array_push($emissionsPassees, $emission);
                        foreach($utilisateurs as $anim) {
                            if($emission->animateur == $anim->utilisateur_id) {
                                array_push($animateurs, $anim);
                            } 
                        }
                        foreach($programmes as $prgm) {
                            if($emission->programme_id == $prgm->programme_id) {
                                array_push($prog, $prgm);
                            }
                        }
                    }
                }
            }
        }

        return $this->render($response, 'Replay.html.twig', ['emissionsPassees' => $emissionsPassees, 'creneaux' => $creneaux, 'creneauPasse' => $creneauPasse, 'programmes' => $prog, 'animateurs' => $animateurs]);

    } //End of function afficherReplay

    public function afficherPlanning($request, $response)
    {
        date_default_timezone_set('Europe/Paris');

        $creneauAvenir = [];
        $emissionsAvenir = [];
        $hActuelle = date("H:i:s");
        $dateActuelle = date("Y-m-d");
        $animateurs = [];
        $prog = [];

        $creneaux = Creneau::all();
        $emissions = Emission::all();
        $utilisateurs = Utilisateur::all();
        $programmes = Programme::all();

        // Parcourir tous les créneaux pour obtenir ceux à venir de la journée ou dans les jours qui viennent
        foreach ($creneaux as $creneau) {
           if (($creneau->heure_debut > $hActuelle && $creneau->date_creneau >= $dateActuelle) || $creneau->date_creneau > $dateActuelle) {
                // Tableau des créneaux à venir de la journée et des jours à venir
                array_push($creneauAvenir, $creneau);
            }
        }

        // Parcours des émissions afin d'obtenir le nom, description... des émissions à venir
        foreach($emissions as $emission) {
            // Si il y a des créneau à venir
            if(!empty($creneauAvenir)) {
                // Pour obtention des propriétés des émissions à venir
                foreach ($creneauAvenir as $prochainCreneau) {
                    if($prochainCreneau->emission_id == $emission->emission_id) {
                        // Tableau des propriétés des émissions à venir
                        array_push($emissionsAvenir, $emission);
                        foreach($utilisateurs as $anim) {
                            if($emission->animateur == $anim->utilisateur_id) {
                                array_push($animateurs, $anim);
                            } 
                        }
                        foreach($programmes as $prgm) {
                            if($emission->programme_id == $prgm->programme_id) {
                                array_push($prog, $prgm);
                            }
                        }
                    }
                }
            }
        }

        return $this->render($response, 'Planning.html.twig', ['emissionsAvenir' => $emissionsAvenir, 'creneaux' => $creneaux, 'creneauAvenir' => $creneauAvenir, 'animateurs' => $animateurs, 'programmes' => $prog]);
    } //End of function afficherPlanning
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

    public function afficherComptePass($request, $response, $args)
    {

        $user = Utilisateur::find($args['id']);
        return $this->render($response, 'MonComptePass.html.twig', ['utilisateur' => $user]);
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
    } //End of function gererInscription

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
    } //End of function gererConnexion

    public function updateMdp($request, $response, $args)
    {
        
        //recuperation des donnees du post
        $password1 = (isset($_POST['newPass1'])) ? $_POST['newPass1'] : null;
        $password2 = (isset($_POST['newPass2'])) ? $_POST['newPass2'] : null;
        $mdpUpdated = '';


        try {
            if (!isset($password1) || !isset($password2) || empty($password1) || empty($password2)) {
                throw new \Exception("Tous les champs doivent être complétés");
            }
            if ($password1 != $password2) {
                throw new \Exception("Les mots de passe ne correspondent pas");
            }


            $user = Utilisateur::find($args['id']);
            
            if(!isset($user) || empty($user)) {
                throw new \Exception("Utilisateur inconnu");
            }

            $password1 = password_hash($password1, PASSWORD_DEFAULT);
            $user->password = $password1;


            $user->save();
            unset($password1);
            unset($password2);
            $mdpUpdated = 'ok';

        } catch (\Exception $e) {
            return $this->render($response, 'MonComptePass.html.twig', ['erreur' => $e->getMessage()]);

        }

        return $this->render($response, 'MonCompte.html.twig', ['utilisateur' => $user, 'updateMdp' => $mdpUpdated]);
    } //End of function updateMdP

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
        $titreEmissionMtn = "";
        $descriptionEmissionMtn = "";

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
                    $titreEmissionMtn = $emission->titre;
                    $descriptionEmissionMtn = $emission->resume;

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

        return $this->render($response, 'Direct.html.twig', ['emissionMtn' => $emissionMtn, 'emissionsAvenir' => $emissionsAvenir, 'emissionsPassees' => $emissionsPassees, 'creneaux' => $creneaux, 'creneauMtn' => $creneauMtn, 'creneauAvenir' => $creneauAvenir, 'creneauPasse' => $creneauPasse, 'tempsAvantEmis' => $tempsAvantEmis, 'heureProchEmiss' => $heureProchaineEmission, 'titreProchaineEmis' => $titreProchaineEmission, 'titreEmissionMtn' => $titreEmissionMtn, 'descriptionMtn' => $descriptionEmissionMtn]);
    } //End of function ecouterDirect

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

        // test si le login existe
        $login = Utilisateur::find($postId);

        $login->identifiant   = $newlog;

        $login->save();
        return json_encode($login);
    }
}
