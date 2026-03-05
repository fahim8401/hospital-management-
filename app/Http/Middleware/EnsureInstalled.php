<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Redirect to the installer when the app has not been installed yet.
 */
class EnsureInstalled
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! file_exists(storage_path('installed'))) {
            return redirect()->route('install.requirements');
        }

        return $next($request);
    }
}
