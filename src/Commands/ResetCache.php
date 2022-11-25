<?php

namespace Anfragen\Permission\Commands;

use Anfragen\Permission\Facades\CacheKeys;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class ResetCache extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'anfragen:reset-cache';

    /**
     * The console command description.
     */
    protected $description = 'Reset the cache';

    /**
     * Execute the console command.
     *
     */
    public function handle(): int
    {
        Cache::forget(CacheKeys::roles());

        Cache::forget(CacheKeys::modelRoles());

        Cache::forget(CacheKeys::permissions());

        Cache::forget(CacheKeys::permissionRoles());

        Cache::forget(CacheKeys::modelPermissions());

        return Command::SUCCESS;
    }
}
