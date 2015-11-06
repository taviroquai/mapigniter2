<?php namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use App\Brand;

class BrandController extends BaseController
{
    /**
     * Brands index
     * 
     * @return \Illuminate\View\View
     */
    public function index()
	{
		return view('admin/brands');
	}
    
    /**
     * Edit brand
     * 
     * @param Brand $brand
     * @return \Illuminate\View\View
     */
    public function form(Brand $brand)
	{
		return view('admin/brands-edit', compact('brand'));
	}
    
    /**
     * Save brand
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function save()
    {
        $input = \Input::except('_token');
        $input['active'] = empty($input['active']) ? 0 : 1;
        
        // Validate
        $validator = \Validator::make($input, [
            'name' => 'required|max:255'
        ]);
        
        // When fails
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->messages()]);
        }
        
        // Save changes
        $brand = Brand::findOrNew($input['id']);
        
        // Uncheck other brands default value
        if ($input['active']) {
            \DB::table('brands')->update(['active' => 0]);
        }
        $brand->fill($input);
        $brand->save();
        
        // Process logo image
        if ($picture = \Request::file('file-0')) {
            $filename = 'picture.'.$picture->getClientOriginalExtension();
            $picture->move(public_path($brand->getStoragePath()), $filename);
            $brand->logo = $filename;
            $brand->save();
        }
        
        // At least 1 brand must be active
        if (\DB::table('brands')->where('active', 1)->count() == 0) {
            $first = \DB::table('brands')->first();
            \DB::table('brands')->where('id', $first->id)->update(['active' => 1]);
        }
        
        try {
            // Save active css
            $active = Brand::where('active', 1)->first();
            file_put_contents($active->getCssPath(), $active->css);

            // Response
            return response()->json(['success' => 'Brand saved', 'redirect' => url('/admin/brands/list')]);
        } catch (\Exception $e) {
            // Response
            return response()->json(['success' => false, 'errors' => ['css' => ['Failed saving css']]]);
        }
    }
    
    /**
     * Get all brands
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function json()
	{
		return response()->json(['data' => Brand::all()]);
	}
    
    /**
     * Delete brand
     * 
     * @param Brand $brand
     * @return \Illiminate\Http\RedirectResponse
     */
    public function delete(Brand $brand)
    {
        if ($brand->id !== 1) {
            $brand->delete();
        }
        return redirect('admin/brands/list');
    }

}
