<?php


namespace auditeur\models;


use Illuminate\Database\Eloquent\SoftDeletes;

class Utilisateur extends \Illuminate\Database\Eloquent\Model
{

    protected $table = 'UTILISATEUR';
    protected $primaryKey = 'utilisateur_id';
    public $timestamps = false;
    use SoftDeletes;
    protected $dates = ['deleted_at'];

    public function emissions(){
        return $this->hasMany("auditeur\models\Emission");
    }

}
