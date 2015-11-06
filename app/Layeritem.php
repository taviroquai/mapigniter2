<?php

namespace App;

class Layeritem extends Layer
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'layeritem';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['map_id', 'parent_id', 'layer_id', 'visible', 'displayorder'];
    
    /**
     * Maps relation
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function map()
    {
        return $this->belongsTo('App\Map');
    }
    
    /**
     * Layer relation
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function layer()
    {
        return $this->belongsTo('App\Layer');
    }
    
    /**
     * Group relation
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function group()
    {
        return $this->belongsTo('App\Layer', 'parent_id');
    }
    
}
