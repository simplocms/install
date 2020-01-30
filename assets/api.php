<?php

if (isset($_POST['db_type'])) {
    DbConnection::getInstance()->setDriver($_POST['db_type']);
    $cookieTime = time() + 3600; // 1 hour
    setcookie('db_type', $_POST['db_type'], $cookieTime, "/");

    if (isset($_POST['db_host'])) {
        DbConnection::getInstance()->setHost($_POST['db_host']);
        setcookie('db_host', $_POST['db_host'], $cookieTime, "/");
    }

    if (isset($_POST['db_port'])) {
        DbConnection::getInstance()->setPort($_POST['db_port']);
        setcookie('db_port', $_POST['db_port'], $cookieTime, "/");
    }

    if (isset($_POST['db_name'])) {
        DbConnection::getInstance()->setDbName($_POST['db_name']);
        setcookie('db_name', $_POST['db_name'], $cookieTime, "/");
    }

    if (isset($_POST['db_user'])) {
        DbConnection::getInstance()->setUser($_POST['db_user']);
        setcookie('db_user', $_POST['db_user'], $cookieTime, "/");
    }

    if (isset($_POST['db_password'])) {
        DbConnection::getInstance()->setPassword($_POST['db_password']);
        setcookie('db_password', $_POST['db_password'], $cookieTime, "/");
    } else {
        DbConnection::getInstance()->setPassword('');
        setcookie('db_password', '', $cookieTime, "/");
    }

    $connection = DbConnection::getInstance()->getConnection();

    if ($connection instanceof PDO) {
        echo 'Connection is succesfull';
    } else {
        echo $connection->getMessage();
    }

    exit;
}
