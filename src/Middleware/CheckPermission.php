<?php

namespace Anfragen\Permission\Middleware;

use Anfragen\Permission\Exceptions\PermissionBlockException;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CheckPermission
{
    public function handle(Request $request, Closure $next, string $permissions): mixed
    {
        $permissions = Str::of($permissions)->explode('|')->toArray();

        if (!$request->user()->canAny($permissions)) {
            throw PermissionBlockException::handle($permissions);
        }

        return $next($request);
    }
}
