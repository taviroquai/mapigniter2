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
    
    /**
     * Get published maps
     * 
     * @return array
     */
    static function getPublishedItems()
    {
        $items = Map::with(['content' => function ($query) {
            $query->orWhere(function($query) {
                $query->where('publish_start', '<', date('Y-m-d'));
                $query->where('publish_end', '<', date('Y-m-d'));
            })
            ->orWhere(function($query) {
                $query->where('publish_start', '<', date('Y-m-d'));
                $query->whereNull('publish_end');
            })
            ->orWhere(function($query) {
                $query->whereNull('publish_start');
                $query->where('publish_end', '>', date('Y-m-d'));
            });
        }])
        ->get();
        return $items;
    }
    
}
