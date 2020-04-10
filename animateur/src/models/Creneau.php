<?php


namespace animateur\models;


use Illuminate\Database\Eloquent\SoftDeletes;

class Creneau extends \Illuminate\Database\Eloquent\Model
{

    protected $table = 'creneau';
    protected $primaryKey = 'creneau_id';
    public $timestamps = false;
    use SoftDeletes;
    protected $dates = ['deleted_at'];

    public function emission(){
        return $this->hasOne("animateur\models\Emission");
    }
}
