<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class Cors
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        return $next($request)->header("Access-Control-Allow-Origin", "*")
            ->header("Access-Control-Allow-Methods", "GET,POST,PUT,DELETE,PATH,OPTIONS")
            ->header("Access-Control-Allow-Headers", ", Content-Type, Authorization");
            
        // $response = $next($request);
        // $response->headers->set('Access-Control-Allow-Origin', '*');
        // $response->headers->set('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
        // $response->headers->set('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, X-Token-Auth, Authorization');
        
        // // Agregar esta línea para permitir la incrustación del PDF en un iframe
        // $response->headers->set('X-Frame-Options', 'ALLOWALL');
        
        // return $response;
    }
}
