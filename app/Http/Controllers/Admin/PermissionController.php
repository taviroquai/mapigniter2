<?php namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use App\Permission;

class PermissionController extends BaseController
{
    
    /**
     * Get permission index
     * 
     * @return type
     */
    public function index()
	{
		return view('admin/permissions');
	}
    
    /**
     * Edit permission
     * 
     * @param Permission $permission
     * @return \Illuminate\View\View
     */
    public function form(Permission $permission)
	{
		return view('admin/permissions-edit', compact('permission'));
	}
    
    /**
     * Save permission
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function save()
    {
        $input = \Input::except('_token');
        $validator = \Validator::make($input, [
            'label' => 'required|max:255',
            'route' => 'required|max:255'
        ]);
        
        // When fails
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->messages()]);
        }
        
        // Save changes
        $role = empty($input['id']) ? new Permission : Permission::find($input['id']);
        $role->fill($input);
        $role->save();
        
        // Response
        return response()->json(['success' => 'Settings saved', 'redirect' => url('/admin/permissions/list')]);
    }
    
    /**
     * Get all permissions
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function json()
	{
		return response()->json(['data' => Permission::all()]);
	}
    
    /**
     * Delete permission
     * 
     * @param Permission $permission
     * @return \Illiminate\Http\RedirectResponse
     */
    public function delete(Permission $permission)
    {
        $permission->delete();
        return redirect('admin/permissions/list');
    }
    
    public function downloadLogs()
    {
        return response()->download(base_path('storage/logs/laravel.log'));
    }

}
