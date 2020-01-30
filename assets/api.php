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

    $connection = DbConnection::getInstance()->getConnection();

    if ($connection instanceof PDO) {
        echo 'Connection is succesfull';
    } else {
        echo $connection->getMessage();
    }

    exit;
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
