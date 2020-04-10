<?php


namespace auditeur\models;


use Illuminate\Database\Eloquent\SoftDeletes;

class Favoris extends \Illuminate\Database\Eloquent\Model
{

    protected $table = 'favoris';
    protected $primaryKey = 'favoris_id';
    public $timestamps = false;
    use SoftDeletes;
    protected $dates = ['deleted_at'];

}
