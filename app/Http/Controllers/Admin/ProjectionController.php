<?php namespace App\Http\Controllers\Admin;

use App\Projection;

class ProjectionController extends AdminController
{
    /**
     * Get projections index
     * 
     * @return \Illuminate\View\View
     */
    public function index()
	{
		return view('admin/projections');
	}
    
    /**
     * Edit projection
     * 
     * @param Projection $projection
     * @return \Illuminate\View\View
     */
    public function form(Projection $projection)
	{
        $projection->srid = empty($projection->srid) ? 3857 : $projection->srid;
        $extent = empty($projection->extent) ? ['-20026376.39', '-20048966.10', '20026376.39', '20048966.10'] : explode(' ', $projection->extent);
		return view('admin/projections-edit', compact('projection', 'extent'));
	}
    
    /**
     * Save projection
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function save()
    {
        $input = \Input::except('_token');
        
        // Try to get proj4 from postgis
        if (empty($input['proj4_params'])) {
            try {
                $input['proj4_params'] = \DB::table('public.spatial_ref_sys')->where('srid', $input['srid'])->pluck('proj4text');
            } catch (\Exception $e) {
                ;
            }
        }
        
        // Validate map content
        $validator = \Validator::make($input, [
            'extent' => 'required|max:255'
        ]);

        // When validation fails
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->messages()]);
        }
        
        // Load content
        if (empty($input['id'])) {
            $projection = new Projection;
        } else {
            $projection = Projection::find($input['id']);
        }
        
        // Save changes
        $input['extent'] = implode(' ', $input['extent']);
        $projection->fill($input);
        $projection->save();
        
        // Response
        return response()->json(['success' => 'Projection saved', 'redirect' => url('/admin/projections/list')]);
    }
    
    /**
     * Get all maps
     * 
     * @return \Illiminate\Http\JsonResponse
     */
    public function json()
	{
		return response()->json(['data' => Projection::all()]);
	}
    
    /**
     * Delete projection
     * 
     * @param Projection $projection
     * @return \Illiminate\Http\RedirectResponse
     */
    public function delete(Projection $projection)
    {
        $projection->delete();
        return redirect('admin/projections/list');
    }

}
