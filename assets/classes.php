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

    public static function checkRequirement(string $ext): bool
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

        return $exists;
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

    public static function checkRequirement(string $module): bool
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
    
        return $exists;
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

    public function getConnection()
    {
        $dsn = $this->getDsn();

        $connection = $dsn . ':host=' . $this->host;
        if ($this->port) {
            $connection .= ';port=' . $this->port;
        }
        
        $connection .= ';dbname=' . $this->dbName;

        try {
            return new PDO($connection, $this->user, $this->password ?: null);
        } catch (PDOException $e) {
            (new CustomException($e->getMessage()))->throw();            
        }
    }

    public function checkConnection($withEmpty = true)
    {
        $pdo = $this->getConnection();

        if (! $withEmpty) {
            return;
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
        $env = fopen('.env', "w");
        if (! $env) {
            (new CustomException("Unable to open file .env"))->throw();
        }

        $content = "APP_NAME=simplocms\n";
        $content .= "APP_ENV=local\n";
        $content .= "APP_DEBUG=true\n";
        $content .= "APP_LOG_LEVEL=debug\n";
        $content .= "APP_URL=http://localhost\n";
        $content .= "APP_KEY=\n";
        $content .= "\n";
        $content .= "DB_CONNECTION=" . $this->getDsn() . "\n";
        $content .= "DB_HOST=" . $this->host . "\n";
        $content .= "DB_PORT=" . $this->port . "\n";
        $content .= "DB_DATABASE=" . $this->dbName . "\n";
        $content .= "DB_USERNAME=" . $this->user . "\n";
        $content .= "DB_PASSWORD=" . $this->password . "\n";
        $content .= "\n";
        $content .= "TESTING_DB_DATABASE=simplo_cms_testing\n";
        $content .= "\n";
        $content .= "MAIL_DRIVER=smtp\n";
        $content .= "MAIL_HOST=smtp.mailtrap.io\n";
        $content .= "MAIL_PORT=25\n";
        $content .= "MAIL_USERNAME=\n";
        $content .= "MAIL_PASSWORD=\n";
        $content .= "MAIL_ENCRYPTION=tls\n";
        $content .= "MAIL_FROM_ADDRESS=simplo@gmail.com\n";
        $content .= "MAIL_FROM_NAME=simplocms\n";
        $content .= "\n";
        $content .= "DEFAULT_LOCALE=cs\n";
        $content .= "ENABLED_LOCALES=cs,en\n";

        fwrite($env, $content);
        fclose($env);
    }

    public function getDsn()
    {
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

        return $dsn;
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

    public function store()
    {
        try {
            $pdo = DbConnection::getInstance()->getConnection();

            // set err mode to exception
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $pwd = password_hash($this->password, PASSWORD_BCRYPT);

            $stmt = $pdo->prepare("SELECT id, name FROM roles WHERE name='administrator'");
            $stmt->execute();

            $stmt->setFetchMode(PDO::FETCH_ASSOC);
    
            foreach(new RecursiveArrayIterator($stmt->fetchAll()) as $k=>$v) {
                $roleId = $v['id'];
                // only first result if there are more
                break;
            }

            if (! isset($roleId)) {
                throw new PDOException('Failed to create admin user, check your database connection.');
            }

            $now = date('Y-m-d H:i:s');

            // begin the transaction
            $pdo->beginTransaction();
            // sql statements
            $sql = "INSERT INTO users (username, firstname, lastname, email, enabled, password, protected, created_at, updated_at, locale)
            VALUES ('$this->login', '$this->firstName', '$this->lastName', '$this->email', TRUE, '$pwd', TRUE, '$now', '$now', 'cs')";
            $pdo->exec($sql);
            $lastId = $pdo->lastInsertId();
            $pdo->exec("INSERT INTO role_user (user_id, role_id)
            VALUES ($lastId, 2)");
            $pdo->commit();


        } catch (PDOException $e) {
            (new CustomException($e->getMessage()))->throw();
        }

        // $pdo = null;
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
    private $code;

    public function __construct($message, $code = 301)
    {
        $this->message = $message;
        $this->code = $code;
    }

    public function throw()
    {
        echo $this->message;
        http_response_code($this->code);
        exit;
    }
}