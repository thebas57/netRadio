<?php


namespace auditeur\models;


use Illuminate\Database\Eloquent\SoftDeletes;

class Emission extends \Illuminate\Database\Eloquent\Model
{

    protected $table = 'EMISSION';
    protected $primaryKey = 'emission_id';
    public $timestamps = false;
    use SoftDeletes;
    protected $dates = ['deleted_at'];


    public function programme(){
        return $this->hasOne("auditeur\models\Programme");
    }

    public function animateur(){
        return $this->hasOne("auditeur\models\Utilisateur");
    }

    public function creneau(){
        return $this->hasOne("auditeur\models\Creneau");
    }

}


