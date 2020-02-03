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

    DbConnection::getInstance()->checkConnection(! isset($_POST['store_admin']));

    if (! isset($_POST['unzip_file']) && ! isset($_POST['store_admin'])) {
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

    if (! isset($_POST['store_admin'])) {
        echo "a";
        exit;
    }
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
    if (execute("php artisan migrate")) {
        //error
        (new CustomException("Couldn't migrate database, check .env file."));
    }

    echo "Database succesfully migrated\n";
    exit;
}

if (isset($_POST['db_seed'])) {
    if (execute("php artisan db:seed")) {
        //error
        (new CustomException("Couldn't seed database, check .env file."));
    }

    echo "Database succesfully seeded\n";
    exit;
}

if (isset($_POST['store_admin'])) {
    Admin::getInstance()->store();

    echo "Admin user created, ready to use.";
    exit;
}
