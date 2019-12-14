<?php

    require_once('php/keys.php');
    require_once('php/login.php');
    require_once('php/functions.php');

    if (isset($_FILES['userfile'])) {
        $uploaddir = 'img/';
        $uploadext = explode('/', $_FILES['userfile']['type'])[1];
        $filename = random_filename(16, $uploaddir, $uploadext);
        $uploadfile = $uploaddir . $filename;
        

        if (move_uploaded_file($_FILES['userfile']['tmp_name'], $uploadfile)) {
            dbprint('Successfully uploaded ' . $uploadfile);
        }
        else {
            dbprint('Failed to upload ' . $uploadfile);
        }

        try {
            $db = new PDO($dsn, $db_user, $db_pw);
            $query = 'INSERT INTO `images` (`img_id`, `img_path`, `img_tagcount`) VALUES (NULL, :img_path, 0);';
            $statement = $db->prepare($query);
            $statement->bindValue(':img_path', $filename);
            $result = $statement->execute();
            $img_id = $db->lastInsertId();
            $statement->closeCursor();
        }
        catch (PDOException $e) {
            echo $e->getMessage();
        }
        $preview = '<img src="' . $uploadfile . '">';
    }

?>

<!doctype html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
        <title>Booru</title>
        <meta name="description" content="A booru (tag-based image board) made from scratch by someone who doesn't know what they're doing. Expect things to either be only partially implemented or outright broken.">
        <link rel="stylesheet" href="css/style.css">
        
    </head>
    <body>
    <?php if(isset($_SESSION['user'])) { ?>
        <?= isset($preview) ? $preview : '' ?>
        <?php if (isset($img_id)) { ?>
        <form method="POST" action="editor.php">
            <input type="submit" value="Tag this image">
            <input type="text" value="<?= $img_id ?>" name="img_id" style="display:none">
        </form>
        <?php } ?>
        <a href="main.php">Back to main</a>
        <a href="images.php">View uploaded images</a>
    <?php } else { echo $login_form; } ?>
    </body>
</html>