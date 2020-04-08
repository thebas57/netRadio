<?php

namespace animateur\controllers;

use animateur\models\Programme;
use animateur\models\Emission;
use animateur\models\Utilisateur;
use animateur\models\Creneau;

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
        $mdp = password_hash("0403", PASSWORD_DEFAULT);
        return $this->render($response, 'Accueil.html.twig', ["mdp" => $mdp]);
    } //End of function afficherAccueil

    /**
     * Fonction permettant d'afficher les creneaux.
     * @param $request
     * @param $response
     * @return mixed
     */
    public function voirCreneau($request, $response)
    {

        $emission = new Emission();
        $anim = new Utilisateur();
        $tab = [];
        $dateActuelle = date("Y-m-d");

        $creneaux = Creneau::all();

        foreach ($creneaux as $key => $creneau) {
            // modif format heure
            $hddModif = date('G:i', strtotime($creneau->heure_debut));
            $creneau->heure_debut = $hddModif;

            $hdfModif = date('G:i', strtotime($creneau->heure_fin));
            $creneau->heure_fin = $hdfModif;

            // Pour récupérer donnée avec clé etrangere
            $emission = Emission::find($creneau->emission_id);
            $anim = Utilisateur::find($emission->animateur);
            $tmp = ['user' => $anim->identifiant, 'emission' => $emission->titre];
            array_push($tab, $tmp);
        }

        unset($emission);
        unset($anim);
        unset($tmp);

        return $this->render($response, 'Creneau.html.twig', ['creneaux' => $creneaux, 'userEmission' => $tab, 'dateAjd' => $dateActuelle]);
    } //End of function voirCreaneau

    /**
     * Fonction permettant d'afficher les creneaux DAUJOUDRDHUI.
     * @param $request
     * @param $response
     * @return mixed
     */
    public function voirCreneauAjd($request, $response)
    {

        $emission = new Emission();
        $anim = new Utilisateur();
        $tab = [];
        $dateActuelle = date("Y-m-d");

        $creneaux = Creneau::where('date_creneau', $dateActuelle)->orderBy('heure_debut')->get();

        foreach ($creneaux as $key => $creneau) {
            // modif format heure
            $hddModif = date('G:i', strtotime($creneau->heure_debut));
            $creneau->heure_debut = $hddModif;

            $hdfModif = date('G:i', strtotime($creneau->heure_fin));
            $creneau->heure_fin = $hdfModif;

            // Pour récupérer donnée avec clé etrangere
            $emission = Emission::find($creneau->emission_id);
            $anim = Utilisateur::find($emission->animateur);
            $tmp = ['user' => $anim->identifiant, 'emission' => $emission->titre];
            array_push($tab, $tmp);
        }

        unset($emission);
        unset($anim);
        unset($tmp);

        return $this->render($response, 'Creneau.html.twig', ['creneaux' => $creneaux, 'userEmission' => $tab, 'dateAjd' => $dateActuelle]);
    } //End of function voirCreaneauAjd

    /**
     * Fonction permettant d'afficher les creneaux de demain.
     * @param $request
     * @param $response
     * @return mixed
     */
    public function voirCreneauDemain($request, $response)
    {

        $emission = new Emission();
        $anim = new Utilisateur();
        $tab = [];
        $dateActuelle = date("Y-m-d");
        $dateDemain = date('Y-m-d', strtotime($dateActuelle . ' + 1 DAY'));
        $creneaux = Creneau::where('date_creneau', $dateDemain)->orderBy('heure_debut')->get();

        foreach ($creneaux as $key => $creneau) {
            // modif format heure
            $hddModif = date('G:i', strtotime($creneau->heure_debut));
            $creneau->heure_debut = $hddModif;

            $hdfModif = date('G:i', strtotime($creneau->heure_fin));
            $creneau->heure_fin = $hdfModif;

            // Pour récupérer donnée avec clé etrangere
            $emission = Emission::find($creneau->emission_id);
            $anim = Utilisateur::find($emission->animateur);
            $tmp = ['user' => $anim->identifiant, 'emission' => $emission->titre];
            array_push($tab, $tmp);
        }

        unset($emission);
        unset($anim);
        unset($tmp);

        return $this->render($response, 'Creneau.html.twig', ['creneaux' => $creneaux, 'userEmission' => $tab, 'dateAjd' => $dateActuelle]);
    } //End of function voirCreneauDemain

    /**
     * Fonction permettant d'afficher les programmes.
     * @param $request
     * @param $response
     * @return mixed
     */
    public function voirProgramme($request, $response)
    {
        $prog = Programme::all();

        return $this->render($response, 'Programme.html.twig', ['programmes' => $prog]);
    } //End of function voirProgramme

    /**
     * Fonction permettant d'afficher les emissions.
     * @param $request
     * @param $response
     * @return mixed
     */
    public function voirEmission($request, $response, $args)
    {
        $user = new Utilisateur();
        $programme = new Programme();
        $tab = [];
        $emissions = Emission::all();
        foreach ($emissions as $key => $emission) {
            $user = Utilisateur::find($emission->animateur);
            $programme = Programme::find($emission->programme_id);
            $tmp = ['user' => $user->identifiant, 'programme' => $programme->nom];
            array_push($tab, $tmp);
        }
        unset($user);
        unset($programme);
        unset($tmp);
        return $this->render($response, 'Emission.html.twig', ['emissions' => $emissions, 'userProg' => $tab]);
    } //End of function voirEmission

    /**
     * Fonction permettant d'ajouter des creneau.
     * @param $request
     * @param $response
     * @return mixed
     */
    public function addCreneau($request, $response)
    {
        try {
            //on récupère les données du formulaire
            $hdd = (!empty($_POST['hdd'])) ? $_POST['hdd'] : null;
            $hdf = (!empty($_POST['hdf'])) ? $_POST['hdf'] : null;
            $date = (!empty($_POST['date'])) ? $_POST['date'] : null;
            $emission = (!empty($_POST['emission'])) ? $_POST['emission'] : null;

            //on verifie que les champs sont tous remplis
            if (!isset($hdd) || !isset($hdf) || !isset($date) || !isset($emission))
                throw new \Exception("un champs requis n'a pas été rempli");

            //on filtre les données
            $hdd = filter_var($hdd, FILTER_SANITIZE_STRING);
            $hdf = filter_var($hdf, FILTER_SANITIZE_STRING);
            $date = filter_var($date, FILTER_SANITIZE_STRING);
            $emission = filter_var($emission, FILTER_SANITIZE_STRING);

            //Verif si heure ok
            if ($hdd > $hdf) {
                throw new \Exception("L'heure de début ne peut pas être supérieur à celle de fin voyons...");
            }

            // Verif si un creneau est déjà occupé
            $dateActuelle = date("Y-m-d");

            //$creneaux = Creneau::where('date_creneau', $dateActuelle)->orderBy('heure_debut')->get();
            $creneaux = Creneau::all();

            foreach ($creneaux as $creneau) {
                if ($creneau->date_creneau == $date) {
                    if (
                        $creneau->heure_debut <= $hdd && $creneau->heure_fin <= $hdd ||
                        $creneau->heure_debut >= $hdf && $creneau->heure_fin >= $hdf
                    ) {
                    } else {
                        throw new \Exception("Il existe déjà un créneau dans cette tranche d'horaire");
                    }
                }
            }

            //on les insère en bdd
            $creneau = new Creneau();
            $creneau->heure_debut = $hdd;
            $creneau->heure_fin = $hdf;
            $creneau->date_creneau = $date;
            $creneau->emission_id = $emission;
            $creneau->save();

            //libération des variables
            unset($hdf);
            unset($hdd);
            unset($date);
            unset($emission);

            //redirection
            $creneau = Creneau::all();
            return $this->redirect($response, 'creneau');
        } catch (\Exception $e) {
            die($e->getMessage());
        }
    } //end of function addCreneau



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
     * Fonction permettant d'afficher l'ajout des creneaux.
     * @param $request
     * @param $response
     * @return mixed
     */
    public function afficherAddCreneau($request, $response, $args)
    {
        $creneau = Creneau::all();
        $emission = Emission::all();
        return $this->render($response, 'AddCreneau.html.twig', ['creneaux' => $creneau, 'emissions' => $emission]);
    } //End of function afficherAddCreneau

    /**
     * Fonction permettant d'afficher la modif des creneaux.
     * @param $request
     * @param $response
     * @return mixed
     */
    public function afficherModifCreneau($request, $response, $args)
    {
        $id = Creneau::find(intVal($args['id']));
        $emission = Emission::all();
        return $this->render($response, 'ModifCreneau.html.twig', ['creneau' => $id, 'emissions' => $emission]);
    } //End of function afficherModifCreneau

    /**
     * Fonction permettant de modifier des creneaux.
     * @param $request
     * @param $response
     * @return mixed
     */
    public function modifCreneau($request, $response, $args)
    {

        $creneau = Creneau::find(intVal($args['id']));

        //on les insère en bdd
        $creneau->heure_debut = $_POST['hdd'];
        $creneau->heure_fin = $_POST['hdf'];
        $creneau->date_creneau = $_POST['date'];
        $creneau->emission_id = $_POST['emission'];
        $creneau->save();

        //redirection
        $creneau = Creneau::all();
        return $this->redirect($response, 'creneau');
    } //end of function modifCreneau

    /**
     * Fonction permettant d'afficher la modif des creneaux.
     * @param $request
     * @param $response
     * @return mixed
     */
    public function afficherModifEmission($request, $response, $args)
    {
        $id = Emission::find(intVal($args['id']));
        $programmes = Programme::all();
        $utilisateurs = Utilisateur::all();
        return $this->render($response, 'ModifEmission.html.twig', ['emission' => $id, 'programmes' => $programmes, 'utilisateurs' => $utilisateurs]);
    } //End of function afficherModifCreneau

    /**
     * Fonction permettant de modifier des emissions.
     * @param $request
     * @param $response
     * @return mixed
     */
    public function modifEmission($request, $response, $args)
    {

        $emission = Emission::find(intVal($args['id']));

        //on les insère en bdd
        $emission->titre = $_POST['titre'];
        $emission->resume = $_POST['resume'];
        $emission->fichier = $_POST['fichier'];
        $emission->animateur = $_POST['animateur'];
        $emission->programme_id = $_POST['programme'];
        $emission->save();

        //redirection
        $emission = Emission::all();
        return $this->redirect($response, 'emission');
    } //end of function modifEmission

    /**
     * Fonction permettant d'afficher la modif des creneaux.
     * @param $request
     * @param $response
     * @return mixed
     */
    public function afficherModifProgramme($request, $response, $args)
    {
        $id = Programme::find(intVal($args['id']));
        return $this->render($response, 'ModifProgramme.html.twig', ['programme' => $id]);
    } //End of function afficherModifCreneau

    /**
     * Fonction permettant de modifier des programmes.
     * @param $request
     * @param $response
     * @return mixed
     */
    public function modifProgramme($request, $response, $args)
    {

        $programme = Programme::find(intVal($args['id']));

        //on les insère en bdd
        $programme->nom = $_POST['nom'];
        $programme->description = $_POST['desc'];
        $programme->save();

        //redirection
        $programme = Programme::all();
        return $this->redirect($response, 'programme');
    } //end of function modifProgramme

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
    public function afficherAddEmission($request, $response, $args)
    {
        $prog = Programme::all();
        $anim = Utilisateur::where('droit', 2)->get();
        return $this->render($response, 'AddEmission.html.twig', ['programmes' => $prog, 'utilisateurs' => $anim]);
    } //End of function addEmission

    /**
     * Fonction permettant de supprimer des creneaux.
     * @param $request
     * @param $response
     * @return mixed
     */
    public function supprCreneau($request, $response, $args)
    {
        $id = $args['id'];
        $creneau = Creneau::find(intVal($id));
        $creneau->delete();
    } //End of function supprCreneau

    /**
     * Fonction permettant de supprimer des programmes.
     * @param $request
     * @param $response
     * @return mixed
     */
    public function supprProgramme($request, $response, $args)
    {

        /*
        $emissions = DB::table('emission') 
            ->join('emission','emission.programme_id','=',$id)->get();

        foreach ($emissions as $key => $emission) {
            $emission->delete();
        }

        //$emissions->delete();
        */

        $id = $args['id'];

        $programme = Programme::find(intVal($id));
        $programme->delete();

        $emissions = Emission::where('programme_id', intval($id))->get();
        foreach ($emissions as $key => $emission) {
            $emission->delete();
        }
        /*
        

        foreach ($emissions as $key => $emission) {
            $emission = $emissions::where($id, '=',$emissions->programme_id)->get();
            $emission->delete();
        }
        */
    } //End of function supprCreneau

    /**
     * Fonction permettant de supprimer des emissions.
     * @param $request
     * @param $response
     * @return mixed
     */
    public function supprEmission($request, $response, $args)
    {
        $id = $args['id'];
        $emission = Emission::find(intVal($id));
        $creneaux = Creneau::where('emission_id', intval($id))->get();
        if (count($creneaux) > 0) {
            throw new \Exception("Impossible, cette émission est attribuée à un créneau");
        } else {
            $emission->delete();
            return $this->redirect($response, 'emission');
        }
    } //End of function supprEmission

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

    public function afficherConnexion($request, $response)
    {
        return $this->render($response, 'Connexion.html.twig');
    } //End of function afficherConnexion

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

            if ($user->droit == 0) {
                throw new \Exception("Vous n'avez pas le droit d'accéder à cette zone !");
            }
            $_SESSION['user'] = ['id' => $user->utilisateur_id, 'droit' => $user->droit];
            if ($user->droit == 1) {
                return $this->redirect($response, 'accueilAnimateur');
            } else {
                return $this->redirect($response, 'accueil');
            }
        } catch (\Exception $e) {
            return $this->render($response, 'Connexion.html.twig', ['erreur' => $e->getMessage()]);
        }
    } //End of function gererConnexion

    public function deconnexion($request, $response)
    {
        if (isset($_SESSION['user'])) {
            unset($_SESSION['user']);
        }
        return $this->redirect($response, 'accueil');
    } //End of function deconnexion

    public function afficherAjoutStaff($request, $response)
    {
        return $this->render($response, 'AjoutStaff.html.twig');
    } //End of function afficherAjoutStaff

    public function ajoutStaff($request, $response)
    {
        $email = (isset($_POST['email'])) ? $_POST['email'] : null;
        $login = (isset($_POST['identifiant'])) ? $_POST['identifiant'] : null;
        $password = (isset($_POST['password'])) ? $_POST['password'] : null;
        $droit = (isset($_POST['droit'])) ? $_POST['droit'] : null;

        $login = filter_var($login, FILTER_SANITIZE_STRING);
        $email = filter_var($email, FILTER_SANITIZE_EMAIL);
        $droit = filter_var($droit, FILTER_SANITIZE_NUMBER_INT);

        $password = password_hash($password, PASSWORD_DEFAULT);

        $user = new Utilisateur();
        $user->identifiant = $login;
        $user->droit = $droit;
        $user->password = $password;
        $user->email = $email;
        $user->save();

        return $this->redirect($response, 'accueil');
    } //End of function ajoutStaff

    public function afficherListeUsers($request, $response)
    {

        $users = Utilisateur::all();

        return $this->render($response, 'VoirUsers.html.twig', ['users' => $users]);
    } //End of function afficherListeUsers

    public function supprUser($request, $response, $args)
    {
        $id = $args['id'];

        if (is_numeric($id)) {
            $user = Utilisateur::find(intval($id));
            if (isset($user)) {
                $user->delete();
            }
        }
    } //End of function supprUser

}
