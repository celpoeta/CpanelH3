<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class VerifyAccessKey
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle($request, Closure $next)
    {
        // Obtenemos el api-key que el usuario envia
        $key = $request->headers->get('api_key');
        // Si coincide con el valor almacenado en la aplicacion
        // la aplicacion se sigue ejecutando
        //if (isset($key) == env('API_KEY')) {
            return $next($request);
        /*} else {
            // Si falla devolvemos el mensaje de error
            return response()->json(['error' => 'unauthorized' ], 401);
        }*/
    }
}
