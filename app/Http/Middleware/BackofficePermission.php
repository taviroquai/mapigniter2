<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Contracts\Routing\Middleware;
use App\Permission;

/**
 * Description of BackofficePermission
 *
 * @author mafonso
 */
class BackofficePermission implements Middleware {
    
    /**
     * Run the request filter.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $route = \Route::getCurrentRoute(); // $route->uri(), $request->path()
        $roles = \Auth::user()->roles;
        $allow = true;
        
        try {
            // Check if route has permission
            foreach ($roles as $role) {
                foreach($role->permissions as $permission) {
                    $allow = $allow & !$this->denied($request, $route, $permission);
                }
            }
        } catch (\Exception $e) {
            \Log::error($e->getFile().':'.$e->getLine().' '.$e->getMessage());
            $allow = false;
        }
        
        // Apply access
        \Log::info('ACCESS:' .\Auth::user()->name.':'. $request->method().':'.$request->path().':'.($allow ? 'ALLOWED' : 'DENIED'));
        if (!$allow) {
            if ($request->ajax()) {
                return response('Unauthorized.', 401);
            } else {
                return response(view('admin/unauthorized'), 401);
            }
        } else {
            return $next($request);
        }
    }
    
    protected function denied($request, $route, $permission)
    {
        $pattern = $permission->route;
        if (strstr($pattern, '{') || strstr($pattern, '/')) {
            $pattern = str_replace(['{', '}', '?', '/'], ['\{', '\}', '\?', '\/'], $pattern);
        }
        $pattern = "/".$pattern."/i";
        if ($request->isMethod($permission->http)
            && $permission->pivot->access == 'DENY'
            && (preg_match($pattern, $route->uri()) || preg_match($pattern, $request->path()))
        ) {
            return true;
        }
        return false;
    }
    
}
