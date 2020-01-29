<?php

class Requirements
{
    public const ITEMS = [
        'php' => 'PHP >= 7.1.3',
        'mysql' => ' Mysql database ^5.6',
        'pdo' => 'PDO PHP Extension',
        'tokenizer' => 'Tokenizer PHP Extension',
        'mbstring' => 'Mbstring PHP Extension',
        'openssl' => 'OpenSSL PHP Extension',
        'xml' => 'XML PHP Extension',
        'ctype' => 'Ctype PHP Extension',
        'gd' => 'GD PHP Extension',
        'json' => 'JSON PHP Extension',
        'bcmath' => 'BCMath PHP Extension',
        'curl' => 'cURL PHP Extension',
        'zip' => 'ZipArchive PHP Library is required'
    ];

    public static function checkRequirement(string $ext, string $label)
    {
        $exists = false; 
        switch ($ext) {
            case 'php':
                $exists = checkPhpVersion('7.1.3');
                break;
            case 'mysql':
                $exists = checkMysqlVersion();
                break;
            case 'pdo':
                $exists = defined('PDO::ATTR_DRIVER_NAME');
                break;
            case 'tokenizer':
                $exists = extension_loaded('tokenizer');
                break;
            case 'mbstring':
                $exists = extension_loaded('mbstring');
                break;
            case 'openssl':
                $exists = extension_loaded('openssl');
                break;
            case 'xml':
                $exists = extension_loaded('xml');
                break;
            case 'ctype':
                $exists = extension_loaded('ctype');
                break;
            case 'gd':
                $exists = extension_loaded('gd');
                break;
            case 'json':
                $exists = function_exists('json_encode');
                break;
            case 'bcmath':
                $exists = function_exists('bcadd');
                break;
            case 'curl':
                $exists = function_exists('curl_init');
                break;
            case 'zip':
                $exists = class_exists('ZipArchive');
                break;
        }

        echo $label . '  -->   ' . $exists;
    }
}

class Optional
{
    public const ITEMS = [
        'imagick' => 'Imagick PHP Extension',
        'optim' => 'JpegOptim',
        'opti' => 'Optipng',
        'quant' => 'Pngquant 2',
        'svgo' => 'SVGO',
        'gif' => 'Gifsicle'
    ];

    public static function checkRequirement(string $module, string $label)
    {
        $exists = false;
        switch ($module) {
            case 'imagick':
            $exists = class_exists('Imagick');
                break;
            case 'optim':
                if ($output = shell_exec('jpegoptim -v')) {
                    $exists = true;
                } else {
                    $exists = false;
                }
                break;
            case 'opti':
                if ($output = shell_exec('optipng -v')) {
                    $exists = true;
                } else {
                    $exists = false;
                }
                break;
            case 'quant':
                if ($output = shell_exec('pngquant --version')) {
                    if ($output[0] == 2) {
                        $exists = true;
                    } else {
                        $exists = false;
                    }
                } else {
                    $exists = false;
                }
                break;
            case 'svg':
                if ($output = shell_exec('svgo -v')) {
                    $exists = true;
                } else {
                    $exists = false;
                }
                break;
            case 'gif':
                if ($output = shell_exec('gifsicle -v')) {
                    $exists = true;
                } else {
                    $exists = false;
                }
                break;
        }
    
        echo $label . '  -->   ' . $exists;
    }
}

class DbConnection
{
    private static $instance = null;

    public const DRIVERS = [
        'MySQL',
        'PostgreSQL',
        'SQLite',
        'SQL Server'
    ];

    private $driver = 'MySQL';
    private $host = '';
    private $user = '';
    private $password = '';
    private $dbName = '';
    private $port = '3306';

    private function __construct()
    {
        //
    }

    public static function getInstance()
    {
        if (! self::$instance) {
            self::$instance = new DbConnection();
        }

        return self::$instance;
    }

    public function getConnection() {
        $dsn = 'mysql';
        switch ($this->driver) {
            case 'MySql':
                $dsn = 'mysql';
                break;
            case 'PostgreSQL':
                $dsn = 'pgsql';
                break;
            case 'SQLite':
                $dsn = 'sqlite';
                break;
            case 'SQL Server':
                $dsn = 'dblib';
                break;
        }

        $connection = $dsn . ':host=' . $this->host . ';port=' . $this->port . ';dbname=' . $this->dbName;

        try {
            return new PDO($connection, $this->user, $this->password);
        } catch (PDOException $e) {
            return null;
        }
    }

    public  function setDriver(string $driver)
    {
        $this->driver = $driver;
    }

    public  function setHost(string $host)
    {
        $this->host = $host;
    }

    public  function setUser(string $user)
    {
        $this->user = $user;
    }

    public  function setPassword(string $password)
    {
        $this->password = $password;
    }

    public  function setDatabase(string $dbName, string $port)
    {
        $this->dbName = $dbName;
        $this->port = $port;
    }
}