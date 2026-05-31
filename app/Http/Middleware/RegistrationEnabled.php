<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RegistrationEnabled
{
    public function handle(Request $request, Closure $next): Response
    {
        if (setting('registration_enabled', 'true') !== 'true') {
            abort(403, '注册功能已关闭');
        }

        return $next($request);
    }
}
