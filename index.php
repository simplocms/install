<?php include 'assets/functions.php'; ?>
<?php require_once 'assets/classes.php'; ?>
<?php include 'assets/api.php' ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Simplo cms installer</title>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css">
    
    <style><?php include 'assets/styles.css'; ?></style>
</head>
<body>
    <div class="bg-info text-primary text-center py-5">
        <div class="container">
            <h1>SIMPLOCMS</h1>
            <h4>Welcome to SIMPLO CMS. Before getting started, we'll check if the server meets all the installation requirements.</h4>
            <p>Having trouble? Check our <a href="#" class="text-primary">manual installation</a></p>
        </div>
    </div>

    <div class="container show tab" id="tab-1">
        <div class="container">
            <h5>
                X System requirements
            </h5>
            <div>
                <ul>
                    <?php
                        $canContinue = true;
                        foreach (Requirements::ITEMS as $key => $label) {
                    ?>
                            <li>
                    <?php
                                $req = Requirements::checkRequirement($key, $label);
                                echo $label . '  -->   ' . $req;

                                if ($canContinue) {
                                    $canContinue = $req;
                                }
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
                if ($perm = checkDirPermissions()) {
                    echo 'Permissions are good';
                } else {
                    $canContinue = $perm;
                    echo 'Permissions are bad';
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
                                $req = Optional::checkRequirement($key, $label);
                                echo $label . '  -->   ' . $req;
                    ?>
                            </li>
                    <?php
                        }
                    ?>
                </ul>
            </div>
        </div>

        <a href="/" class="btn btn-info">Check again</a>
        <button class="btn btn-info btn-next" <?php if (! $canContinue) echo 'disabled'?> data-next='2'>Next</button>
    </div>

    <div class="container d-none tab" id="tab-2">
        <h5>Database</h5>
        <div class="p-4 card">
            <form action="index.php" method="POST" id="dbForm">
                <div class="form-group">
                    <label for="db_type">Database type</label>
                    <select name="db_type" id="db_type" class="form-control">
                        <?php
                            foreach (DbConnection::DRIVERS as $driver) {
                                ?>
                                <option value="<?php echo $driver; ?>"
                                <?php if (DbConnection::getInstance()->getDriver() === $driver) echo 'selected'?>
                                ><?php echo $driver; ?></option>
                                <?php
                            }
                        ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="db_host">Host</label>
                    <input type="text" class="form-control" name="db_host" id="db_host" value="<?php echo DbConnection::getInstance()->getHost() ?>" placeholder="localhost">
                </div>

                <div class="form-group">
                    <label for="db_port">Port (optional)</label>
                    <input type="text" class="form-control" name="db_port" id="db_port" value="<?php echo DbConnection::getInstance()->getPort() ?>" placeholder="3306">
                </div>

                <div class="form-group">
                    <label for="db_name">Database name</label>
                    <input type="text" class="form-control" name="db_name" id="db_name" value="<?php echo DbConnection::getInstance()->getDbName() ?>" placeholder="db_simplo">
                </div>

                <div class="form-group">
                    <label for="db_user">Login</label>
                    <input type="text" class="form-control" name="db_user" id="db_user" value="<?php echo DbConnection::getInstance()->getUser() ?>" placeholder="root">
                </div>

                <div class="form-group">
                    <label for="db_password">Password</label>
                    <input type="text" class="form-control" name="db_password" id="db_password" value="<?php echo DbConnection::getInstance()->getPassword() ?>">
                </div>

                <button class="btn btn-info" id="dbButton">Check connection</button>
                <span id="dbResult"></span>
            </form>
        </div>

        <button class="btn btn-info btn-prev" data-next='1'>Prev</button>
        <button class="btn btn-info btn-next" data-next='3' disabled>Next</button>
    </div>

    <div class="container d-none tab" id="tab-3">
        <h5>Administrator</h5>
        <div class="p-4 card">
            <form action="index.php" method="POST" id="adminForm">
                <div class="form-group">
                    <label for="admin_first_name">First name</label>
                    <input type="text" class="form-control" name="admin_first_name" id="admin_first_name" value="<?php echo Admin::getInstance()->getFirstName() ?>">
                    <span class="error"></span>
                </div>

                <div class="form-group">
                    <label for="admin_last_name">Last name</label>
                    <input type="text" class="form-control" name="admin_last_name" id="admin_last_name" value="<?php echo Admin::getInstance()->getLastName() ?>">
                    <span class="error"></span>
                </div>

                <div class="form-group">
                    <label for="admin_email">E-mail</label>
                    <input type="email" class="form-control" name="admin_email" id="admin_email" value="<?php echo Admin::getInstance()->getEmail() ?>">
                    <span class="error"></span>
                </div>

                <div class="form-group">
                    <label for="admin_login">Login</label>
                    <input type="text" class="form-control" name="admin_login" id="admin_login" value="<?php echo Admin::getInstance()->getLogin() ?>">
                    <span class="error"></span>
                </div>

                <div class="form-group">
                    <label for="admin_password">Password</label>
                    <input type="password" class="form-control" name="admin_password" id="admin_password" value="<?php echo Admin::getInstance()->getPassword() ?>">
                    <span id="adminPasswordError"></span>
                </div>

                <div class="form-group">
                    <label for="admin_password_confirm">Confirm password</label>
                    <input type="password" class="form-control" name="admin_password_confirm" id="admin_password_confirm" value="<?php echo Admin::getInstance()->getPassword() ?>">
                    <span id="adminPasswordConfirmError"></span>
                </div>
                
                <button class="btn btn-info btn-prev" data-next='2'>Prev</button>
                <button class="btn btn-info" id="installButton">Install</button>
                <div class="d-inline-block" id="adminErr"></div>
            </form>
        </div>
    </div>
</body>

<script>
<?php include 'assets/vendorsc.js' ?>
</script>
</html>
<?php
