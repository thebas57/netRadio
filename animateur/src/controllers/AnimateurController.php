<?php

namespace animateur\controllers;

use animateur\models\Creneau;
use animateur\models\Emission;
use animateur\models\Programme;
use animateur\models\Utilisateur;
use Illuminate\Database\Capsule\Manager as DB;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;


class AnimateurController extends BaseController
{
    /**
     * @param $request
     * @param $response
     * @return mixed
     * Cette fonction permet d'afficher l'accueil de l'animateur, avec tous les programmes, ainsi que leurs émissions
     * respectives, qu'il doit animer.
     */
    public function accueil($request, $response)
    {
        $programmes = Programme::all();

        $lesProgrammes = [];

        foreach ($programmes as $programme) {
            $emissions = Emission::where("programme_id", "=", $programme->programme_id)->get();
            foreach ($emissions as $emission) {
                $animateur = Utilisateur::find($emission->animateur);
                if ($animateur) {
                    $id_session = $_SESSION["user"]["id"];
                    if ($animateur->utilisateur_id == $id_session) {
                        $lesProgrammes[] = $programme;
                        break;
                    }
                }
            }
        }

        return $this->render($response, "AccueilAnimateur.html.twig", ["programmes" => $lesProgrammes]);
    }

    /**
     * @param $request
     * @param $response
     * @param $args
     * @return mixed
     * Cette fonction permet d'afficher toutes les émissions à animer
     */
    public function emissionsAAnimer($request, $response, $args)
    {
        $idProgramme = $args["id"];
        $idUser = $_SESSION["user"]["id"];
        $emissions = Emission::where("programme_id", "=", $idProgramme)->where("animateur", "=", $idUser)->get();
        $lesEmissions = [];
        foreach ($emissions as $emission) {
            $creneau = Creneau::where("emission_id", "=", $emission->emission_id)->first();


            //conversion dates et time en fr
            list($year, $month, $day) = explode("-", $creneau->date_creneau);
            $dateCreneau = "${day}/${month}/${year}";
            list($hour, $minut, $sec) = explode(":", $creneau->heure_debut);
            $heureDebut = "${hour}h${minut}";
            list($hour, $minut, $sec) = explode(":", $creneau->heure_fin);
            $heureFin = "${hour}h${minut}";


            $tmp = [
                "emission_id" => $emission->emission_id,
                "creneau_id" => $creneau->creneau_id,
                "date" => $dateCreneau,
                "debut" => $heureDebut,
                "fin" => $heureFin
            ];
            $lesEmissions[] = $tmp;
        }
        $programme = Programme::find($idProgramme);

        return $this->render($response, "EmissionsAnimateur.html.twig", ["emissions" => $lesEmissions, "programmeNom" => $programme->nom]);
    }

    /**
     * @param $request
     * @param $response
     * @param $args
     * @return mixed
     * Cette fonction va chercher le créneau correspondant à l'émission passée dans l'URL, pour ensuite en afficher les
     * informations de date, et, bien sûr, le programme correspondant, pour l'afficher sur une page.
     */
    public function animerEmission($request, $response, $args)
    {
        $emission_id = $args["id"];
        $emission = Emission::find($emission_id);
        $creneau = Creneau::where("emission_id", "=", $emission_id)->first();

        //conversion dates et time en fr
        list($year, $month, $day) = explode("-", $creneau->date_creneau);
        $dateCreneau = "${day}/${month}/${year}";
        list($hour, $minut, $sec) = explode(":", $creneau->heure_debut);
        $heureDebut = "${hour}h${minut}";
        list($hour, $minut, $sec) = explode(":", $creneau->heure_fin);
        $heureFin = "${hour}h${minut}";

        $programme = Programme::where("programme_id", "=", $emission->programme_id)->first();

        return $this->render($response, "AnimationEmission.html.twig",
            ["creneau" => [
                "date" => $dateCreneau,
                "debut" => $heureDebut,
                "fin" => $heureFin
            ],
                "programmeNom" => $programme->nom,
                "emission_id" => $emission_id
            ]);
    }

    /**
     * @param $request
     * @param $response
     * @return false|string
     * Fonction appelée par le JS lors d'une émission, elle reçoit les fichiers audios (enregistrements) et, permet de les concaténer.
     */
    public function receiveAudio($request, $response)
    {
        $emission_id = (isset($_POST["emission_id"])) ? $_POST["emission_id"] : null;
        $type = (isset($_POST["type"])) ? $_POST["type"] : null;
        $audio = (isset($_FILES["audio"])) ? $_FILES["audio"] : null;

        if (!isset($emission_id) || !isset($audio)) {
            return json_encode(["error" => "un champ requis n'a pas été rempli", "code" => 500]);
        }

        $emission = Emission::find($emission_id);

        if (!isset($emission) || empty($emission)) {
            return json_encode(["error" => "Emission non trouvée", "code" => 404]);
        }

        $tmp = $emission->fichier;
        $titre = $this->escapeSpace($emission->titre);

        if (!isset($emission->fichier)) {
//            $emission->fichier = $audio;
            if ($this->createWorkingDir($emission->titre)) {
                move_uploaded_file($audio['tmp_name'], "./emissions/" . $this->escapeSpace($emission->titre) . "/1.ogg");
                chmod("emissions/" . $this->escapeSpace($emission->titre) . "/1.ogg", 0777);
                $emission->fichier = "emission/" . $this->escapeSpace($emission->titre) . "/1.ogg";
            }

        } else {
            if ($this->createWorkingDir($emission->titre)) {
                //On déplace l'audio )
                $truc = json_decode($emission->fichier);
                if($type == "musique") {
                    if (move_uploaded_file($audio["tmp_name"], "./emissions/" . $titre . "/2.mp3")) {
                        chmod("emissions/" . $titre . "/2.mp3", 0777);
                        //on convertit en ogg
                        exec("ffmpeg -y -i ./emissions/${titre}/2.mp3 -c:a libopus -b:a 19.1k -ac 1 -r 16k ./emissions/${titre}/2.ogg");
                        //On donne les droits
                        chmod("emissions/" . $titre . "/2.ogg", 0777);
                        exec("ffmpeg -y -i ./emissions/${titre}/2.ogg -vn -acodec libopus ./emissions/${titre}/2.ogg");
                        chmod("emissions/" . $titre . "/2.ogg", 0777);
                        chmod("emissions/" . $titre . "/2.ogg", 0777);
                        unlink("emissions/${titre}/2.ogg");

                        //On a réussi à mettre les deux sur le serveur
                        $emission->fichier = $this->concatAudio($emission->titre);
                    }
                }else{
                    if (move_uploaded_file($audio["tmp_name"], "./emissions/" . $this->escapeSpace($emission->titre) . "/2.ogg")) {
                        chmod("emissions/" . $titre . "/2.ogg", 0777);
                        chmod("emissions/" . $titre . "/2.ogg", 0777);

                        //On a réussi à mettre les deux sur le serveur
                        $emission->fichier = $this->concatAudio($emission->titre);
                    }
                }
            }
        }

        $emission->save();

        return json_encode(["emission" => $emission->titre, "fichier" => $emission->fichier, "fichier_old" => $tmp]);

    }

    /**
     * @param $titre : Titre de l'émission, pour le nom du dossier
     * @return string : On retourne le chemin vers le fichier audio crée
     * Fonction qui permet de concaténer deux fichiers audios, soit 1.ogg et 2.ogg soit out.ogg et 2.ogg
     */
    private function concatAudio($titre)
    {
        $titre = $this->escapeSpace($titre);

        //On concat
            if (file_exists("emissions/${titre}/out.ogg")) {
                exec("ffmpeg -i concat:'./emissions/${titre}/out.ogg|./emissions/${titre}/2.ogg' -c copy ./emissions/${titre}/out.ogg -y");
                chmod("emissions/" . $titre . "/out.ogg", 0777);
                chmod("emissions/" . $titre . "/out.ogg", 0777);

            } else {
                exec("ffmpeg -i concat:'./emissions/${titre}/1.ogg|./emissions/${titre}/2.ogg' -c copy ./emissions/${titre}/out.ogg -y");

                if (file_exists("emissions/${titre}/out.ogg")) {
                    chmod("emissions/" . $titre . "/out.ogg", 0777);
                    chmod("emissions/" . $titre . "/out.ogg", 0777);

                    unlink("emissions/${titre}/1.ogg");
                }
            }
        unlink("emissions/${titre}/2.ogg");
        return ("emissions/${titre}/out.ogg");
    }

    /**
     * @param $titre : Nom de l'émission
     * @return bool : On retourne si le fichier existe ou non
     * Fonction permettant de créer un dossier de travail (du nom de l'émission)
     */
    private function createWorkingDir($titre)
    {
        $titre = $this->escapeSpace($titre);
        //vérification de l'existence du dossier
        if (is_dir("emissions/" . $titre)) {
            return true;
        } else {
            //Création du dossier
            $old = umask(0);
            if (mkdir("emissions/" . $titre, 0777, true)) {
                umask($old);
                return true;
            } else {
                //Problèmes à la création
                umask($old);
                return false;
            }
        }
    }

    private function deleteWorkingDir($titre)
    {
        $titre = $this->escapeSpace($titre);
//        Vérification de l'existence du dossier
        if (file_exists("emissions/" . $titre) && is_dir("emissions/" . $titre)) {
            if (rmdir("emissions/" . $titre)) {
                return true;
            } else {
                // Problème à la suppression du dossier (Dossier non vide, etc ...)
                return false;
            }
        } else {
            return true;
        }
    }

    private function escapeSpace($str)
    {
        $tmp = explode(" ", $str);
        $str = "";
        foreach ($tmp as $k => $v) {
            if ($k == count($tmp) - 1) {
                $str .= $v;
            } else {
                $str .= $v . "_";
            }
        }
        return $str;
    }
}

