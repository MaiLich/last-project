<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class Admin
{
        public function handle(Request $request, Closure $next)
    {
        
        
        
        
        

        
        
        
        if (! \Illuminate\Support\Facades\Auth::guard('admin')->check()) { 
            return redirect('/admin/login');
        }


        return $next($request);
    }
}