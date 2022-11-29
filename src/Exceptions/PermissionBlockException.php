<?php

namespace Anfragen\Permission\Exceptions;

use Illuminate\Http\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

class PermissionBlockException extends HttpException
{
    public static function handle(array $permissions): self
    {
        $message = trans('anfragen::permissions.blocked');

        if (config('permissions.permissions_in_exception')) {
            $text = trans('anfragen::permissions.block_permissions', ['permissions' => collect($permissions)->implode(', ')]);

            $message = "{$message} {$text}";
        }

        return new static(Response::HTTP_FORBIDDEN, $message);
    }
}
