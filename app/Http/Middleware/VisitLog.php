<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Contracts\Routing\Middleware;
use App\Visit;

/**
 * Description of BackofficePermission
 *
 * @author mafonso
 */
class VisitLog implements Middleware {
    
    /**
     * Run the request filter.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {   
        $visit = new Visit([
            'http_url' => $request->fullUrl(),
            'http_method' => $request->method(),
            'http_path' => $request->path(),
            'ip' => $request->ip()
        ]);
        
        if ($user = \Auth::user()) {
            $user->visits()->save($visit);
        } else {
            $visit->save();
        }
        
        return $next($request);
    }
    
}
