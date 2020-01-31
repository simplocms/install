<?php

namespace App\Console\Commands;

use App\Models\Module\InstalledModule;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class MigrateModules extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migrate:modules';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrate installed modules';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        InstalledModule::all(['name'])
            ->each(function (InstalledModule $module) {
                if (!$module->checkModuleExists()) {
                    return;
                }

                Artisan::call('module:migrate', [
                    'module' => $module->name
                ]);
            });
    }
}
