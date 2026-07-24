<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Helpers\UrlCrypt;
use Symfony\Component\HttpFoundation\Response;

class UrlObfuscator
{
    /**
     * Handle an incoming request by transparently decoding any obfuscated ID parameters.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $route = $request->route();

        if ($route) {
            $parameters = $route->parameters();
            $updated = false;

            foreach ($parameters as $key => $value) {
                if (is_string($value) && !is_numeric($value)) {
                    $decoded = UrlCrypt::decode($value);
                    if ($decoded !== $value) {
                        $route->setParameter($key, $decoded);
                        $updated = true;
                    }
                }
            }
        }

        return $next($request);
    }
}
