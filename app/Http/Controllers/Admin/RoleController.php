<?php namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use App\Role;

class RoleController extends BaseController
{
    
    /**
     * Get role index
     * 
     * @return \Illuminate\View\View
     */
    public function index()
	{
		return view('admin/roles');
	}
    
    /**
     * Edit role
     * 
     * @param Role $role
     * @return \Illuminate\View\View
     */
    public function form(Role $role)
	{
		return view('admin/roles-edit', compact('role'));
	}
    
    /**
     * Save role
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function save()
    {
        $input = \Input::except('_token');
        $validator = \Validator::make($input, [
            'name'      => 'required|max:255'
        ]);
        
        // When fails
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->messages()]);
        }
        
        // Save changes
        $role = empty($input['id']) ? new Role : Role::find($input['id']);
        $role->fill($input);
        $role->save();
        
        // Update Permissions
        $role->permissions()->detach();
        if (!empty($input['permissions'])) {
            foreach((array)$input['permissions'] as $id => &$item) {
                $role->permissions()->attach($id, ['access' => $item["'access'"]]);
            }
        }
        
        // Response
        return response()->json(['success' => 'Settings saved', 'redirect' => url('/admin/roles/list')]);
    }
    
    /**
     * Get all roles
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function json()
	{
		return response()->json(['data' => Role::all()]);
	}
    
    /**
     * Delete role
     * 
     * @param Role $role
     * @return \Illiminate\Http\RedirectResponse
     */
    public function delete(Role $role)
    {
        if ($role->id !== 1) {
            $role->delete();
        }
        
        return redirect('admin/roles/list');
    }

}
