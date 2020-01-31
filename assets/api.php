<?php

if (isset($_POST['db_type'])) {
    DbConnection::getInstance()->setDriver($_POST['db_type']);

    if (isset($_POST['db_host'])) {
        DbConnection::getInstance()->setHost($_POST['db_host']);
    }

    if (isset($_POST['db_port'])) {
        DbConnection::getInstance()->setPort($_POST['db_port']);
    }

    if (isset($_POST['db_name'])) {
        DbConnection::getInstance()->setDbName($_POST['db_name']);
    }

    if (isset($_POST['db_user'])) {
        DbConnection::getInstance()->setUser($_POST['db_user']);
    }

    if (isset($_POST['db_password'])) {
        DbConnection::getInstance()->setPassword($_POST['db_password']);
    } else {
        DbConnection::getInstance()->setPassword('');
    }

    DbConnection::getInstance()->getConnection();

    if (! isset($_POST['unzip_file']) ) {
        exit;
    }
}

if (isset($_POST['admin_first_name'])) {
    Admin::getInstance()->setFirstName($_POST['admin_first_name']);

    if (isset($_POST['admin_last_name'])) {
        Admin::getInstance()->setLastName($_POST['admin_last_name']);
    }

    if (isset($_POST['admin_email'])) {
        Admin::getInstance()->setEmail($_POST['admin_email']);
    }

    if (isset($_POST['admin_login'])) {
        Admin::getInstance()->setLogin($_POST['admin_login']);
    }

    if (isset($_POST['admin_password'])) {
        Admin::getInstance()->setPassword($_POST['admin_password']);
    } else {
        Admin::getInstance()->setPassword('');
    }

    exit;
}

if (isset($_POST['unzip_file'])) {
    file_put_contents("source.zip", fopen("https://www.simplocms.com/source.zip", 'r'));
    
    $zip = new ZipArchive;
    if ($zip->open('source.zip') === TRUE) {
        $zip->extractTo($_SERVER['DOCUMENT_ROOT']);
        $zip->close();

        unlink('source.zip');

        echo "Unziped\n";
    } else {
        (new CustomException('Failed unziping source'))->throw();
    }

    DbConnection::getInstance()->writeEnv();
    echo "Env created\n";
    exit;
}

if (isset($_POST['composer'])) {
    execute("composer install");
    echo "composer installed\n";

    $shell = execute("php artisan key:generate");
    echo $shell;
    exit;
}

if (isset($_POST['migrate'])) {
    execute("php artisan migrate");
    echo "database migrated \n";
    exit;
}

if (isset($_POST['db_seed'])) {
    execute("php artisan db:seed");
    echo "database seeded\n";
    exit;
}

if (isset($_POST['install_npm'])) {
    $os = explode('', trim(php_uname()))[0];

    if (strtolower($os) === 'windows') {
        execute("npm install --no-bin-links");
    } else {
        execute("npm install");
    }

    execute("npm run prod");
    echo "npm installed\n";
    exit;
}