<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class RunTests extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'run-tests {--without-tty : Disable output to TTY}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run tests for CMS';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

        $this->ignoreValidationErrors();
    }


    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $envPath = base_path('.env');

        \Artisan::call('config:clear');
        \Artisan::call('cache:clear');
        if (file_exists($envPath)) {
            $this->removeDuskEnvironment();
            $defaultConnection = config('database.default');

            $content = file_get_contents($envPath);
            $content = str_replace('APP_ENV=' . config('app.env'), 'APP_ENV=testing', $content);
            $content = str_replace('APP_DEBUG=true', 'APP_DEBUG=false', $content);
            $content = str_replace(
                'DB_CONNECTION=' . $defaultConnection, 'DB_CONNECTION=testing', $content
            );
            $content = str_replace(
                "APP_URL=".config('app.url'), "APP_URL=http://localhost:80\n", $content
            );

            file_put_contents($this->getDuskEnvPath(), $content);
        }

        $result = \Artisan::call('dusk', $_SERVER['argv']);

        if (file_exists(base_path('test-reports/result.xml'))) {
            dump(file_get_contents(base_path('test-reports/result.xml')));
        }

        $this->removeDuskEnvironment();

        return $result;
    }


    /**
     * Remove dusk's environment file.
     */
    private function removeDuskEnvironment()
    {
        if (file_exists($this->getDuskEnvPath())) {
            unlink($this->getDuskEnvPath());
        }
    }


    /**
     * Get path to dusk's environment file.
     *
     * @return string
     */
    private function getDuskEnvPath(): string
    {
        return base_path('.env.dusk');
    }
}
