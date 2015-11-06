<?php

namespace App\Http\Controllers\Ldap;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use ErrorException;
use Exception;
use Redirect;
use Auth;
use App\Ldap;
use Validator;
use Input;


/**
 * Description of AuthController
 *
 * @author mafonso
 */
class AuthController extends Controller
{
    
    /**
     * Display login page
     *
     * @return Response
     */
    public function getLogin()
    {
        return view('ldap.login');
    }
    
    /**
     * Post login
     * 
     * @param Request $request
     * @return type
     */
    public function postLogin(Request $request)
    {
        try {
            
            $validator = Validator::make(Input::except('_token'), [
                'username' => 'required|max:255',
                'password' => 'required|min:6',
            ]);
            
            if ($validator->fails()) {
                return Redirect::back()
                    ->withErrors($validator)
                    ->withInput();
            }
            
            // Get input
            $username = $request->get('username');

            // Check authentication
            $ldap = new Ldap();
            if ($ldap->login($username, $request->get('password'))) {

                // Log user in and redirect
                Auth::login($ldap->getUser($username));
                return redirect()->intended('/');
            }
            
            // Login failed
            return Redirect::back()->with('status', 'Wrong username / password');
        
        } catch (Exception $e) {
            return Redirect::back()->with('status', $e->getMessage());
        } catch (ErrorException $e) {
            return Redirect::back()->with('status', $e->getMessage());
        }
    }
    
}
