<?php include 'assets/functions.php'; ?>
<?php require_once 'assets/classes.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Simplo cms installer</title>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css">
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js"></script>
    <?php include 'assets/styles.php'; ?>
</head>
<body>
    <div class="bg-info text-primary text-center py-5">
        <div class="container">
            <h1>SIMPLOCMS</h1>
            <h4>Welcome to SIMPLO CMS. Before getting started, we'll check if the server meets all the installation requirements.</h4>
            <p>Having trouble? Check our <a href="#" class="text-primary">manual installation</a></p>
        </div>
    </div>

    <div class="container">
        <h5>
            X System requirements
        </h5>
        <div>
            <ul>
                <?php 
                    foreach (Requirements::ITEMS as $key => $label) {
                ?>
                        <li>
                <?php
                            Requirements::checkRequirement($key, $label);
                ?>
                        </li>
                <?php
                    }
                ?>
            </ul>
        </div>
    </div>

    <div class="container">
        <h5>Folder perms</h5>
        <div>
        <?php 
        if (is_writable(__DIR__)) {
            echo "Permissions are good";
        } else {
            echo "Permsissions are bad";
        }
        ?>
        </div>
    </div>

    <div class="container">
        <h5>
            Optional packages
        </h5>
        <div>
            <ul>
                <?php 
                    foreach (Optional::ITEMS as $key => $label) {
                ?>
                        <li>
                <?php
                            Optional::checkRequirement($key, $label);
                ?>
                        </li>
                <?php
                    }
                ?>
            </ul>
        </div>
    </div>

    <div class="container">
        <h5>Database</h5>
        <div class="p-4 card">
            form
        </div>
    </div>
</body>
</html>
<?php
