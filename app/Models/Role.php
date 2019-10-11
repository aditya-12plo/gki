<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{

    protected $table = 'roles';
    protected $primaryKey = 'id';
    protected $fillable = array('role_code','role_name');
    public $timestamps = true;
 
    /**
     * Get the user that owns the role.
     */
    public function users()
    {
        return $this->belongsTo('App\Models\User','role_id');
    }
  
}
