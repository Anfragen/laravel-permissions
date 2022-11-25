<?php

namespace Anfragen\Permission\Commands;

use Anfragen\Permission\Models\Role;
use Illuminate\Console\Command;

class CreateRole extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'anfragen:create-role {name : The name of the role}';

    /**
     * The console command description.
     */
    protected $description = 'Create a new role';

    /**
     * Execute the console command.
     *
     */
    public function handle(): int
    {
        Role::query()->updateOrCreate([
            'name' => $this->argument('name'),
        ]);

        $this->info('Role created successfully.');

        return Command::SUCCESS;
    }
}
