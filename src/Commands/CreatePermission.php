<?php

namespace Anfragen\Permission\Commands;

use Anfragen\Permission\Models\Permission;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class CreatePermission extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'anfragen:create-permission {group : The group of the permission} {name : The name of the permission}';

    /**
     * The console command description.
     */
    protected $description = 'Create a new permission';

    /**
     * Execute the console command.
     *
     */
    public function handle(): int
    {
        Permission::query()->updateOrCreate([
            'group' => $this->argument('group'),
            'name'  => $this->argument('name'),
            'slug'  => Str::slug("{$this->argument('group')} {$this->argument('name')}"),
        ]);

        $this->info('Permission created successfully.');

        return Command::SUCCESS;
    }
}
