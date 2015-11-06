<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'permissions';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['label', 'access', 'http', 'route'];
    
    /**
     * Role relation
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function roles()
    {
        return $this->belongsToMany('App\Role');
    }
    
    /**
     * Checks if has role
     * 
     * @param stdClass $role
     * @return boolean
     */
    public function hasRole($role)
    {
        return $this->roles->contains($role->id);
    }
}
