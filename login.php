<?php

    require_once 'php/paths.php';
    require_once CONFIG_FILE;

    session_start();

    if(isset($_POST['logout'])) {
        session_unset();
        session_destroy();
        session_start();
    }

    $login_error = '';
    if (isset($_POST['username']) && isset($_POST['password'])) {
        $db = new PDO(DSN, DB_USER, DB_PW);
        $query = 'SELECT COUNT(1) AS total FROM `users` WHERE `username` = :username AND `password` = :password';
        $statement = $db->prepare($query);
        $statement->bindValue(':username', $_POST['username']);
        $statement->bindValue(':password', $_POST['password']);
        $statement->execute();

        if ($statement->fetch()['total'] == 1) {
            $_SESSION['user'] = $_POST['username'];
        }
        else {
            $login_error = '<p>Login error</p>';
        }
    }

    if (isset($_SESSION['user'])) {
        header('Location: /booru/index.php');
        exit();
    }
?>

<!doctype html>
<html lang="en">
    <?php if(isset($head_html)) echo $head_html; ?>
    </head>
    <body>
        <div id="login-form">
            <h2>booru v0.2</h2>
            <form method="POST" action="login.php">
                <table>
                <tr>
                    <td>Username</td>
                    <td><input type="text" name="username"></td>
                </tr>
                <tr>
                    <td>Password</td>
                    <td><input type="password" name="password"></td>
                </tr>
                <tr>
                    <td></td>
                    <td><input type="submit" value="Log in"></td>
                </tr>
                <tr>
                    <td></td>
                    <td><?= $login_error ?></td>
                </tr>
                </table>
            </form>
        </div>
    </body>
</html>