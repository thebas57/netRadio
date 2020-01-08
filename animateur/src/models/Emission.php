<?php


namespace animateur\models;


use Illuminate\Database\Eloquent\SoftDeletes;

class Emission extends \Illuminate\Database\Eloquent\Model
{

    protected $table = 'EMISSION';
    protected $primaryKey = 'emission_id';
    public $timestamps = false;
    use SoftDeletes;
    protected $dates = ['deleted_at'];


    public function programme(){
        return $this->hasOne("animateur\models\Programme");
    }

    public function animateur(){
        return $this->hasOne("animateur\models\Utilisateur");
    }

    public function creneau(){
        return $this->hasOne("animateur\models\Creneau");
    }

}


