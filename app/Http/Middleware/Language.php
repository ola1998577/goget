<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;


class Language
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
     public function handle($request, Closure $next)
  {
     // Check header request and determine localizaton
     $local = ($request->hasHeader('X-localization')) ? $request->header('X-localization') : 'en';
     // set laravel localization
     app()->setLocale($local);
    // continue request
    return $next($request);
  }
}
