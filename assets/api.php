<?php

if (isset($_POST['db_type'])) {
    DbConnection::getInstance()->setDriver(filter_var($_POST['db_type'], FILTER_SANITIZE_STRING));

    if (isset($_POST['db_host'])) {
        DbConnection::getInstance()->setHost(filter_var($_POST['db_host'], FILTER_SANITIZE_STRING));
    }

    if (isset($_POST['db_port'])) {
        DbConnection::getInstance()->setPort(filter_var($_POST['db_port'], FILTER_SANITIZE_STRING));
    }

    if (isset($_POST['db_name'])) {
        DbConnection::getInstance()->setDbName(filter_var($_POST['db_name'], FILTER_SANITIZE_STRING));
    }

    if (isset($_POST['db_user'])) {
        DbConnection::getInstance()->setUser(filter_var($_POST['db_user'], FILTER_SANITIZE_STRING));
    }

    if (isset($_POST['db_password'])) {
        DbConnection::getInstance()->setPassword(filter_var($_POST['db_password'], FILTER_SANITIZE_STRING));
    } else {
        DbConnection::getInstance()->setPassword('');
    }

    DbConnection::getInstance()->checkConnection(! isset($_POST['store_admin']));

    if (! isset($_POST['create_env']) && ! isset($_POST['store_admin'])) {
        exit;
    }
}

if (isset($_POST['admin_first_name'])) {
    Admin::getInstance()->setFirstName(trim(filter_var($_POST['admin_first_name'], FILTER_SANITIZE_STRING)));

    if (isset($_POST['admin_last_name'])) {
        Admin::getInstance()->setLastName(trim(filter_var($_POST['admin_last_name'], FILTER_SANITIZE_STRING)));
    }

    if (isset($_POST['admin_email'])) {
        Admin::getInstance()->setEmail(trim(filter_var($_POST['admin_email'], FILTER_SANITIZE_EMAIL)));
    }

    if (isset($_POST['admin_login'])) {
        Admin::getInstance()->setLogin(trim(filter_var($_POST['admin_login'], FILTER_SANITIZE_STRING)));
    }

    if (isset($_POST['admin_password'])) {
        Admin::getInstance()->setPassword($_POST['admin_password']);
    } else {
        Admin::getInstance()->setPassword('');
    }

    if (! isset($_POST['store_admin'])) {
        (new CustomException('Admin creditentials are good', 200))->throw();
    }
}

if (isset($_POST['unzip_file'])) {
    file_put_contents("source.zip", fopen("https://www.simplocms.com/source.zip", 'r'));
    
    $zip = new ZipArchive;
    if ($zip->open('source.zip') === TRUE) {
        $zip->extractTo($_SERVER['DOCUMENT_ROOT']);
        $zip->close();

        unlink('source.zip');

        (new CustomException('Unziped', 200))->throw();
    }

    (new CustomException('Failed unziping source'))->throw();
}

if (isset($_POST['create_env'])) {
    DbConnection::getInstance()->writeEnv();

    if (execute("php artisan key:generate")) {
        //error
        (new CustomException("Couldn't create .env file."));
    }

    (new CustomException('Env created', 200))->throw();
}

if (isset($_POST['migrate'])) {
    if (execute("php artisan migrate")) {
        //error
        (new CustomException("Couldn't migrate database, check .env file."));
    }

    (new CustomException('Database succesfully migrated', 200))->throw();
}

if (isset($_POST['db_seed'])) {
    if (execute("php artisan db:seed")) {
        //error
        (new CustomException("Couldn't seed database, check .env file."));
    }

    (new CustomException('Database seeded', 200))->throw();
}

if (isset($_POST['store_admin'])) {
    Admin::getInstance()->store();

    (new CustomException('Admin user created, ready to use.', 200))->throw();
}

if (isset($_POST['ob_check'])) {
    $exists = Requirements::checkRequirement($_POST['ob_check']);

    if ($exists) {
        (new CustomException('existuje', 200))->throw();
    } else {
        (new CustomException('neexistuje', 200))->throw();
    }
}

if (isset($_POST['op_check'])) {
    $exists = Optional::checkRequirement($_POST['op_check']);

    if ($exists) {
        (new CustomException('existuje', 200))->throw();
    } else {
        (new CustomException('neexistuje', 200))->throw();
    }
}
