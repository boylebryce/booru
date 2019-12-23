<?php

    session_start();

    if(isset($_POST['logout'])) {
        session_unset();
        session_destroy();
        session_start();
    }

    if (isset($_SESSION['user'])) {
        header('Location: index.php');
        exit();
    }

    require_once('php/keys.php');

    $login_error = '';
    if (isset($_POST['username']) && isset($_POST['password'])) {
        $db = new PDO($dsn, $db_user, $db_pw);
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
?>

<!doctype html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Booru</title>
        <meta name="description" content="A booru (tag-based image board) made from scratch by someone who doesn't know what they're doing. Expect things to either be only partially implemented or outright broken.">
        <link rel="stylesheet" href="css/style.css">
        <link rel="stylesheet" href="css/index.css">
        <script src="js/scripts.js"></script>
    </head>
    <body>
        <div id="login-form">
            <h2>One Piece Booru v0.2</h2>
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