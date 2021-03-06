<?php


namespace animateur\models;


use Illuminate\Database\Eloquent\SoftDeletes;

class Utilisateur extends \Illuminate\Database\Eloquent\Model
{

    protected $table = 'utilisateur';
    protected $primaryKey = 'utilisateur_id';
    public $timestamps = false;
    use SoftDeletes;
    protected $dates = ['deleted_at'];

    public function emissions(){
        return $this->hasMany("animateur\models\Emission");
    }

    /*
    public function identifiant() 
    {
        return $this->leftJoin('utilisateur', 'utilisateur.utilisateur_id', '=', 'emission.animateur')->value('identifiant');
    }

    */
}
