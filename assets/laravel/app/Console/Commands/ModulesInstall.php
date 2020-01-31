<?php

namespace App\Console\Commands;

use App\Models\Module\InstalledModule;
use App\Structures\Enums\SingletonEnum;
use Illuminate\Console\Command;

class ModulesInstall extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'module:install {module*}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install modules';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $moduleNames = $this->argument('module');

        foreach ($moduleNames as $moduleName) {
            /** @var \App\Models\Module\Module $module */
            $module = SingletonEnum::modules()->find($moduleName);

            if (!$module) {
                $this->error("Module '$moduleName' not found!");
                continue;
            }

            $installed = InstalledModule::findNamed($moduleName);
            if ($installed) {
                $this->warn("Module '$moduleName' is already installed!");
                continue;
            }

            $module->install();
            $this->info("Module '$moduleName' installed.");
        }
    }
}
