<?php

namespace Anfragen\Permission\Commands;

use Anfragen\Permission\Models\Permission;
use Illuminate\Console\Command;

class CreatePermission extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'anfragen:create-permission {name : The name of the permission}';

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
            'name' => $this->argument('name'),
        ]);

        $this->info('Permission created successfully.');

        return Command::SUCCESS;
    }
}
