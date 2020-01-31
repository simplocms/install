<?php

namespace App\Console\Commands;

use App\Models\Module\InstalledModule;
use App\Structures\Enums\SingletonEnum;
use Illuminate\Console\Command;

final class FixPaths extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fix:paths';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fixes paths of installed modules and default theme.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        SingletonEnum::theme()->checkAndFixPublicLink();

        InstalledModule::all(['name'])
            ->each(function (InstalledModule $module) {
                if (!$module->checkModuleExists()) {
                    return;
                }

                $module->getModuleAttribute()->checkAndFixPublicLink();
            });

        $this->info('Done!');
    }
}
