<?php

namespace App;

class Map extends Content
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'maps';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['projection_id', 'center', 'zoom'];
    
    /**
     * Layers relation
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function layers()
    {
        return $this->hasMany('App\Layeritem');
    }
    
    public function content()
    {
        return $this->belongsTo('App\Content');
    }
    
    public function projection()
    {
        return $this->hasOne('App\Projection', 'srid', 'projection_id');
    }
    
}
