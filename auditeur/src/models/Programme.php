<?php


namespace auditeur\models;


use Illuminate\Database\Eloquent\SoftDeletes;

class Programme extends \Illuminate\Database\Eloquent\Model
{

    protected $table = 'PROGRAMME';
    protected $primaryKey = 'programme_id';
    public $timestamps = false;
    use SoftDeletes;
    protected $dates = ['deleted_at'];

    public function emissions(){
        return $this->hasMany("auditeur\models\Emission");
    }


}
