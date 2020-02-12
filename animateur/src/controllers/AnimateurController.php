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
    public function accueil($request, $response){
        $programmes = Programme::all();

        $_SESSION["user"] = ["id"=>1, "droit" => 1];

        $lesProgrammes = [];

        foreach ($programmes as $programme){
            $emissions = Emission::where("programme_id", "=", $programme->programme_id)->get();
            foreach ($emissions as $emission){
                $animateur = Utilisateur::find($emission->animateur);
                if($animateur){
                    $id_session = $_SESSION["user"]["id"];
                    if($animateur->utilisateur_id == $id_session){
                        $lesProgrammes[] = $programme;
                        break;
                    }
                }
            }
        }

        return $this->render($response, "AccueilAnimateur.html.twig", ["programmes" => $lesProgrammes]);
    }

    public function emissionsAAnimer($request, $response, $args){
        $idProgramme = $args["id"];
        $idUser = $_SESSION["user"]["id"];
        $emissions = Emission::where("programme_id", "=", $idProgramme)->where("animateur", "=", $idUser)->get();
        $lesEmissions = [];
        foreach ($emissions as $emission){
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

    public function animerEmission($request, $response, $args){
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

    public function addSongEmission($request,$response){
        $id = (!empty($_POST['emission_id'])) ? $_POST['emission_id'] : null;
        $song = (!empty($_FILES['song'])) ? $_FILES['song'] : null;
        $id = intval($id);
        $emission = Emission::where("emission_id",$id)->first();

        $emission->fichier = $song;
        $emission->save();
        return json_encode($emission);
    }
}
