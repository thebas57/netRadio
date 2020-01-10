<?php

namespace animateur\controllers;

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
}
