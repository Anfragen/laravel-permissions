<?php

namespace Anfragen\Permission\Exceptions;

use Exception;
use Illuminate\Http\Response;

class RoleBlockException extends Exception
{
    public static function handle(array $roles): self
    {
        $message = trans('permissions::permission.block');

        if (config('permissions.roles_in_exception')) {
            $message .= trans('permissions::permission.block_roles', ['roles' => collect($roles)->implode(', ')]);
        }

        return new static($message, Response::HTTP_FORBIDDEN);
    }
}
