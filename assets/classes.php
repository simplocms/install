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
                exec('jpegoptim -v', $output, $val);
                $exists = $val == 0 ? true : false;
                break;
            case 'opti':
                exec('optipng -v', $output, $val);
                $exists = $val == 0 ? true : false;
                break;
            case 'quant':
                exec('pngquant --version', $output, $val);
                $exists = ($val == 0) && ($output[0][0] == 2) ? true : false;
                break;
            case 'svg':
                exec('svgo -v', $output, $val);
                $exists = $val == 0 ? true : false;
                break;
            case 'gif':
                exec('gifsicle -v', $output, $val);
                $exists = $val == 0 ? true : false;
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
    private $host = 'localhost';
    private $user = 'root';
    private $password = '';
    private $dbName = 'db_simplo';
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
            case 'MySQL':
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

        $connection = $dsn . ':host=' . $this->host;
        if ($this->port) {
            $connection .= ';port=' . $this->port;
        }
        
        $connection .= ';dbname=' . $this->dbName;

        try {
            $pdo = new PDO($connection, $this->user, $this->password ?: null);
        } catch (PDOException $e) {
            (new CustomException($e->getMessage()))->throw();            
        }

        // empty database
        if ($this->driver == 'PostgreSQL') {
            $rows = $pdo->query("select table_name from information_schema.tables where table_schema = 'public'", PDO::FETCH_NUM);
        } else if ($this->driver == 'SQLite') {
            $rows = $pdo->query("select name from sqlite_master where type='table'", PDO::FETCH_NUM);
        }
        elseif ($this->driver === 'SQL Server') {
            $rows = $pdo->query("select [table_name] from information_schema.tables", PDO::FETCH_NUM);
        }
        elseif ($this->driver === 'MySQL') {
            $rows = $pdo->query('show tables', PDO::FETCH_NUM);
        } else {
            (new CustomException('Unknowkn database driver ' . $this->driver))->throw();
        }

        $tableExists = false;
        while ($rows->fetch()) {
            (new CustomException('Database ' . $this->dbName . ' is not empty!'))->throw();
        }
    }

    public function setDriver(string $driver)
    {
        $this->driver = $driver;
    }

    public function setHost(string $host)
    {
        $this->host = $host;
    }

    public function setUser(string $user)
    {
        $this->user = $user;
    }

    public function setPassword(string $password)
    {
        $this->password = $password;
    }

    public function setDbName(string $dbName)
    {
        $this->dbName = $dbName;
    }

    public function setPort(string $port)
    {
        $this->port = $port;
    }

    public function getDriver()
    {
        return $this->driver;
    }

    public function getHost()
    {
        return $this->host;
    }

    public function getUser()
    {
        return $this->user;
    }

    public function getPassword()
    {
        return $this->password;
    }

    public function getDbName()
    {
        return $this->dbName;
    }

    public function getPort()
    {
        return $this->port;
    }

    public function writeEnv()
    {
        $env = fopen(__DIR__ . '/laravel/.env', "w");
        if (! $env) {
            (new CustomException("Unable to open file .env"))->throw();
        }

        $content = "APP_NAME=simplocms\n";
        $content .= "APP_ENV=production\n";
        $content .= "APP_DEBUG=false\n";
        $content .= "APP_URL=http://localhost\n";
        $content .= "\n";
        $content .= "DB_CONNECTION=" . $this->driver . "\n";
        $content .= "DB_HOST=" . $this->host . "\n";
        $content .= "DB_PORT=" . $this->port . "\n";
        $content .= "DB_DATABASE=" . $this->dbName . "\n";
        $content .= "DB_USERNAME=" . $this->user . "\n";
        $content .= "DB_PASSWORD=" . $this->password . "\n";
        $content .= "\n";
        $content .= "MAIL_DRIVER=smtp\n";
        $content .= "MAIL_HOST=smtp.mailtrap.io\n";
        $content .= "MAIL_PORT=25\n";
        $content .= "MAIL_USERNAME=\n";
        $content .= "MAIL_PASSWORD=\n";
        $content .= "MAIL_ENCRYPTION=tls\n";
        $content .= "MAIL_FROM_ADDRESS=simplo@gmail.com\n";
        $content .= "MAIL_FROM_NAME=simplocms\n";

        fwrite($env, $content);
        fclose($env);
    }
}

class Admin
{
    private static $instance = null;
    
    private $firstName = '';
    private $lastName = '';
    private $email = '';
    private $login = '';
    private $password = '';

    private function __construct()
    {
        //
    }

    public static function getInstance()
    {
        if (! self::$instance) {
            self::$instance = new Admin();
        }

        return self::$instance;
    }

    public function setFirstName(string $firstName)
    {
        $this->firstName = $firstName;
    }

    public function setLastName(string $lastName)
    {
        $this->lastName = $lastName;
    }

    public function setEmail(string $email)
    {
        $this->email = $email;
    }

    public function setPassword(string $password)
    {
        $this->password = $password;
    }

    public function setLogin(string $login)
    {
        $this->login = $login;
    }

    public function getFirstName()
    {
        return $this->firstName;
    }

    public function getLastName()
    {
        return $this->lastName;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function getPassword()
    {
        return $this->password;
    }

    public function getLogin()
    {
        return $this->login;
    }
}

class CustomException
{
    private $message;

    public function __construct($message)
    {
        $this->message = $message;
    }

    public function throw()
    {
        echo $this->message;
        http_response_code(301);
        exit;
    }
}