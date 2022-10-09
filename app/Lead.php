<?php
namespace App;

use Jenssegers\Mongodb\Eloquent\Model;

class Lead extends Model
{
   protected $connection = 'mongodb';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'source', 'owner', 'created_by'
    ];
}