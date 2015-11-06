<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'roles';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'content_permission'];
    
    /**
     * Users relation
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function users()
    {
        return $this->belongsToMany('App\User');
    }
    
    /**
     * Permissions relation
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function permissions()
    {
        return $this->belongsToMany('App\Permission')->withPivot('access');
    }

    /**
     * Get role permission
     * 
     * @param stdClass $permission
     * @return boolean
     */
    public function getPermission($permission)
    {
        return $this->permissions->find($permission->id);
    }
    
    /**
     * Check if permission is ALLOW
     * 
     * @param stdClass $permission
     * @return boolean
     */
    public function isPermissionAllow($permission)
    {
        if ($perm = $this->getPermission($permission)) {
            return $perm->pivot->access == 'ALLOW' ? true : false;
        }
        return true;
    }
    
    /**
     * Check if value permission is selected
     * 
     * @param string $value
     * @return boolean
     */
    public function isContentPermission($value)
    {
        if (empty($this->content_permission) && $value == 'NONE') {
            return true;
        }
        return $this->content_permission == $value;
    }
    
}
