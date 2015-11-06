<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Page extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'pages';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'route', 'active'];
    
    /**
     * Get relative storage path
     * 
     * @return string
     */
    public function getRelativePath()
    {
        return 'pages';
    }
    
    /**
     * Get view name
     * 
     * @return string
     */
    public function getView()
    {
        return $this->getRelativePath().'/'.$this->name;
    }
    
    /**
     * Get data file path
     * 
     * @return string
     */
    public function getDataPath()
    {
        return base_path('resources/views/'.$this->getRelativePath().'/'.$this->name.'.data.php');
    }

    /**
     * Get view file path
     * 
     * @return string
     */
    public function getViewPath()
    {
        return base_path('resources/views/'.$this->getView().'.blade.php');
    }
}
