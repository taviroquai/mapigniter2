<?php

namespace App;

class Projection extends Content
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'projections';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['srid', 'proj4_params', 'extent'];
    
    /**
     * Layers relation
     * 
     * @return \Illuminate\Database\Eloquent\Relations\hasMany
     */
    public function layers()
    {
        return $this->belongsTo('App\Layer');
    }
    
    /**
     * Maps relation
     * 
     * @return \Illuminate\Database\Eloquent\Relations\hasMany
     */
    public function maps()
    {
        return $this->belongsTo('App\Map');
    }

    static function sridOptions()
    {
        try {
            $items = \DB::table('public.spatial_ref_sys')->select('srid')->get();
        } catch (\Exception $e) {
            $projections = json_decode(file_get_contents(storage_path('app/projections.json')));
            $items = $projections->items;
        }
        return $items;
    }
    
}
