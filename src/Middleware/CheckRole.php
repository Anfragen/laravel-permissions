<?php

namespace Anfragen\Permission\Middleware;

use Anfragen\Permission\Exceptions\RoleBlockException;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CheckRole
{
    public function handle(Request $request, Closure $next, string $roles): mixed
    {
        $roles = Str::of($roles)->explode('|')->toArray();

        if (!$request->user()->hasAnyRole($roles)) {
            throw RoleBlockException::handle($roles);
        }

        return $next($request);
    }
}
