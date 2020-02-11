<?php


namespace auditeur\models;


use Illuminate\Database\Eloquent\SoftDeletes;

class Creneau extends \Illuminate\Database\Eloquent\Model
{

    protected $table = 'CRENEAU';
    protected $primaryKey = 'creneau_id';
    public $timestamps = false;
    use SoftDeletes;
    protected $dates = ['deleted_at'];

    public function emission(){
        return $this->hasOne("auditeur\models\Emission");
    }
}
