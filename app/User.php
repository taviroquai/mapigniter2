<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'users';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
        'twitter_id', 'facebook_id', 'gplus_id'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * Roles relation
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
    
    /**
     * Content relation
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function contents()
    {
        return $this->hasMany('App\Content');
    }
    
    /**
     * Roles relation
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function visits()
    {
        return $this->hasMany('App\Visit');
    }
    
    /**
     * Get user storage path
     * 
     * @return string
     */
    public function getStoragePath()
    {
        return 'storage/user/'.$this->id;
    }
    
    /**
     * Get avatar url
     * 
     * @return string
     */
    public function getAvatarUrl()
    {
        return asset($this->getStoragePath().'/'.$this->avatar);
    }
    
    /**
     * Check if user has avatar
     * 
     * @return boolean
     */
    public function hasAvatar()
    {
        return is_file(public_path($this->getStoragePath().'/'.$this->avatar));
    }
    
    /**
     * Save user avatar
     * 
     * @param null|File $file
     */
    public function saveAvatar($file, $maxWidth = 1024)
    {
        if ($file) {
            $filename = 'avatar.'.$file->getClientOriginalExtension();
            $file->move(public_path($this->getStoragePath()), $filename);
            $this->avatar = $filename;
            $this->save();
            
            // Go resize if not empty
            if (!empty($maxWidth)) {
                $this->resizeImage(public_path($this->getStoragePath().'/'.$filename), $maxWidth);
            }
        }
    }
    
    /**
     * Resize avatar
     * 
     * @param string  $filename
     * @param integer $maxWidth
     * @param integer $quality
     */
    public function resizeImage($filename, $maxWidth = 1024, $quality = 90)
    {
        $img = \Image::make($filename);
        if ($img->width() > $maxWidth) {
            $img->resize($maxWidth, null, function ($constraint) {
                $constraint->aspectRatio();
            });
            $img->save($filename, $quality);
        }
    }
}
