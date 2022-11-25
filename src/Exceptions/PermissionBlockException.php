<?php

namespace Anfragen\Permission\Exceptions;

use Exception;
use Illuminate\Http\Response;

class PermissionBlockException extends Exception
{
    public static function handle(array $permissions): self
    {
        $message = trans('permissions::permission.block');

        if (config('permissions.permissions_in_exception')) {
            $message .= trans('permissions::permission.block_permissions', ['permissions' => collect($permissions)->implode(', ')]);
        }

        return new static($message, Response::HTTP_FORBIDDEN);
    }
}
