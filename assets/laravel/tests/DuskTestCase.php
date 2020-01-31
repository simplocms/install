<?php

namespace Tests;

use Laravel\Dusk\TestCase as BaseTestCase;
use Facebook\WebDriver\Chrome\ChromeOptions;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\Remote\DesiredCapabilities;

abstract class DuskTestCase extends BaseTestCase
{
    use CreatesApplication;

    /**
     * Configured application.
     *
     * @var \Illuminate\Foundation\Application
     */
    private static $configurationApp = null;

    /**
     * Prepare for Dusk test execution.
     *
     * @beforeClass
     * @return void
     */
    public static function prepare()
    {
        if (!env('DUSK_START_BROWSER_MANUALLY', false)) {
            static::startChromeDriver();
        }
    }


    /**
     * Create the RemoteWebDriver instance.
     *
     * @return \Facebook\WebDriver\Remote\RemoteWebDriver
     */
    protected function driver()
    {
        $options = (new ChromeOptions)
            ->addArguments([
                '--no-sandbox',
                '--headless',
                '--window-size=1280x1280',
                '--disable-gpu',
                '--disable-background-networking',
                '--disable-client-side-phishing-detection',
                '--disable-default-apps',
                '--disable-hang-monitor',
                '--disable-popup-blocking',
                '--disable-prompt-on-repost',
                '--disable-sync',
                '--disable-web-resources',
                '--enable-automation',
                '--enable-logging',
                '--ignore-certificate-errors',
                '--load-extension=/tmp/.org.chromium.Chromium.FEaABH/internal',
                '--log-level=7',
                '--metrics-recording-only',
                '--no-first-run',
                '--password-store=basic',
                '--safebrowsing-disable-auto-update',
                '--test-type=webdriver',
                '--use-mock-keychain'
            ]);

        return RemoteWebDriver::create(
            'http://localhost:9515', DesiredCapabilities::chrome()->setCapability(
            ChromeOptions::CAPABILITY, $options
        )
        );
    }


    /**
     * Creates the application.
     *
     * @return \Illuminate\Foundation\Application
     */
    public function createApplication()
    {
        if (is_null(self::$configurationApp)) {
            self::$configurationApp = self::initialize();
        }

        return self::$configurationApp;
    }


    /**
     * Initialize application for the test case.
     *
     * @return \Illuminate\Foundation\Application
     */
    public static function initialize()
    {
        /** @var \Illuminate\Foundation\Application $app */
        $app = require __DIR__ . '/../bootstrap/app.php';

        /** @var \App\Console\Kernel $kernel */
        $kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
        $kernel->bootstrap();
        $kernel->call('migrate', [
            '--database' => 'testing'
        ]);
        self::truncateTables();
        self::seedLanguages();

        return $app;
    }


    /**
     * Clean up the testing environment before the next test.
     *
     * @return void
     */
    public function tearDown()
    {
        if ($this->app) {
            foreach ($this->beforeApplicationDestroyedCallbacks as $callback) {
                call_user_func($callback);
            }
        }

        $this->setUpHasRun = false;

        if (property_exists($this, 'serverVariables')) {
            $this->serverVariables = [];
        }

        $this->afterApplicationCreatedCallbacks = [];
        $this->beforeApplicationDestroyedCallbacks = [];
    }


    /**
     * Create user with specified permissions.
     *
     * @param array $permissions
     * @param array $userAttributes
     * @return \App\Models\User
     */
    protected function createUserWithPermissions(array $permissions, array $userAttributes = [])
    {
        /** @var \App\Models\User $user */
        $user = factory(\App\Models\User::class)->create($userAttributes);
        /** @var \App\Models\Entrust\Role $role */
        $role = factory(\App\Models\Entrust\Role::class)->create();
        $role->saveNamedPermissions($permissions);

        $user->roles()->attach($role->id);

        return $user;
    }


    /**
     * Seed languages.
     */
    static function seedLanguages()
    {
        \App\Models\Web\Language::create([
            'name' => 'Čeština',
            'enabled' => 1,
            'country_code' => 'CZ',
            'language_code' => 'cs',
            'default' => 1
        ]);

        \App\Models\Web\Language::create([
            'name' => 'English',
            'enabled' => 0,
            'country_code' => 'US',
            'language_code' => 'en',
            'default' => 0
        ]);
    }


    /**
     * Truncate all database tables.
     */
    static function truncateTables()
    {
        $tables = \DB::getDoctrineSchemaManager()->listTableNames();

        \DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        foreach ($tables as $table) {
            if ($table === 'migrations') {
                continue;
            }

            \DB::table($table)->truncate();
        }
        \DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }


    /**
     * Update env file.
     */
    static function updateEnvFile()
    {
        $path = base_path('.env');

        if (file_exists($path)) {
            $content = file_get_contents($path);
            str_replace('APP_ENV=local', 'APP_ENV=testing', $content);
            str_replace('APP_DEBUG=true', 'APP_DEBUG=false', $content);
            str_replace('DB_CONNECTION=mysql', 'DB_CONNECTION=testing', $content);
            file_put_contents($path, $content);
        }
    }
}
