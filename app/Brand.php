<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Brand extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'brands';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'slogan', 'description', 'author', 'keywords', 'default', 'css', 'config'];
    
    /**
     * Path for brand images
     * 
     * @return string
     */
    public function getStoragePath()
    {
        return 'storage/brand/'.$this->id;
    }
    
    /**
     * Get logo url
     * 
     * @return string
     */
    public function getPictureUrl()
    {
        return asset($this->getStoragePath().'/'.$this->logo);
    }
    
    /**
     * Check of brand has logo file
     * 
     * @return boolean
     */
    public function hasPicture()
    {
        return is_file(public_path($this->getStoragePath().'/'.$this->logo));
    }
    
    /**
     * Where to save the active brand CSS
     * 
     * @return type
     */
    public function getCssPath()
    {
        return public_path('storage/style.css');
    }
    
}
