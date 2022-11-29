<?php

namespace Anfragen\Permission\Exceptions;

use Illuminate\Http\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

class RoleBlockException extends HttpException
{
    public static function handle(array $roles): self
    {
        $message = trans('anfragen::permissions.blocked');

        if (config('permissions.roles_in_exception')) {
            $text = trans('anfragen::permissions.block_roles', ['roles' => collect($roles)->implode(', ')]);

            $message = "{$message} {$text}";
        }

        return new static(Response::HTTP_FORBIDDEN, $message);
    }
}
