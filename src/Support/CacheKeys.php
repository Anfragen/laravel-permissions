<?php

namespace Anfragen\Permission\Support;

class CacheKeys
{
    /**
     * Return the cache prefix.
     */
    private function prefix(): string
    {
        return config('permission.cache.prefix', 'anfragen::permissions');
    }

    /**
     * Return the cache key with the prefix.
     */
    private function key(string $key): string
    {
        return "{$this->prefix()}::{$key}";
    }

    /**
     * Return the cache key for the roles.
     */
    public function roles(): string
    {
        return $this->key('roles');
    }

    /**
     * Return the cache key for the model roles.
     */
    public function modelRoles(): string
    {
        return $this->key('model::roles');
    }

    /**
     * Return the cache key for the permissions.
     */
    public function permissions(): string
    {
        return $this->key('permissions');
    }

    /**
     * Return the cache key for the permission roles.
     */
    public function permissionRoles(): string
    {
        return $this->key('permission::roles');
    }

    /**
     * Return the cache key for the model permissions.
     */
    public function modelPermissions(): string
    {
        return $this->key('model::permissions');
    }
}
