<?php

    ini_set('session.gc_maxlifetime', 604800);
    session_set_cookie_params(604800);
    session_start();

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

    $login_form = 
    '<div id="login-form">
        <h2>One Piece Booru v0.2</h2>
        <form method="POST" action="index.php">
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
    </div>'
?>