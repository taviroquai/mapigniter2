<?php namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use App\User;

class UserController extends BaseController
{
    
    /**
     * Get users index
     * 
     * @return \Illuminate\View\View
     */
    public function index()
	{
		return view('admin/users');
	}
    
    /**
     * Get current user form
     * 
     * @return \Illuminate\View\View
     */
    public function profile()
	{
		return view('admin/profile', ['user' => \Auth::user()]);
	}
    
    /**
     * Save current user
     * 
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function profileSave(Request $request)
    {
        $user = \Auth::user();
        $validator = \Validator::make($request->all(), [
            'name' => 'required|max:255',
            'email' => 'required|email|max:255|unique:users,email,'.$user->id,
            'password' => 'confirmed|min:6',
        ]);
        
        // When fails
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        
        // When succeeds
        $user->update($request->all());
        if (!empty($request->get('password'))) {
            $user->password = \Hash::make($request->get('password'));
            $user->save();
        }
        
        // Process avatar
        $user->saveAvatar(\Request::file('avatar'), $request->get('image_max_width'));
        
        \Session::set('status', 'Settings saved');
        return \Redirect::to('admin/profile');
    }
    
    /**
     * Edit user
     * 
     * @param User $user
     * @return \Illuminate\View\View
     */
    public function form(User $user)
	{
		return view('admin/users-edit', compact('user'));
	}
    
    /**
     * Save user
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function save()
    {
        $input = \Input::except('_token');
        $validator = \Validator::make($input, [
            'name'      => 'required|max:255',
            'email'     => 'required|email|max:255|unique:users' . (!empty($input['id']) ? ',email,'.$input['id'] : ''),
            'password'  => empty($input['id']) ? 'required|confirmed|min:6' : 'confirmed|min:6'
        ]);
        
        // When fails
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->messages()]);
        }
        
        // Save changes
        $user = empty($input['id']) ? new User : User::find($input['id']);
        $user->fill($input);
        $user->save();
        
        // Change password
        if (!empty($input['password'])) {
            $user->password = \Hash::make($input['password']);
            $user->save();
        }
        
        // Update relations
        if (empty($input['id'])) {
            $input['roles'][] = 3;
        }
        $user->roles()->sync(empty($input['roles']) ? [] : $input['roles']);
        
        // Response
        return response()->json(['success' => 'Settings saved', 'redirect' => url('/admin/users/list')]);
    }
    
    /**
     * Gel all users
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function json()
	{
		return response()->json(['data' => User::all()]);
	}
    
    /**
     * Delete user
     * 
     * @param User $user
     * @return \Illiminate\Http\RedirectResponse
     */
    public function delete(User $user)
    {
        if ($user->id !== 1) {
            $user->delete();
        }
        
        return redirect('admin/users/list');
    }

}
