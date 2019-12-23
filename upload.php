<?php

    require_once('php/keys.php');
    require_once('php/session.php');
    require_once('php/functions.php');
    require_once('php/shared_html.php');

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
    <?php if(isset($head_html)) echo $head_html; ?>
    <body>
        <?php if (isset($nav_html)) echo $nav_html; ?>
        <?php if (isset($preview)) echo $preview; ?>
        <?php if (isset($img_id)) { ?>
            <form method="POST" action="editor.php">
                <input type="submit" value="Tag this image">
                <input type="hidden" value="<?= $img_id ?>" name="img_id">
            </form>
        <?php } ?>
    </body>
</html>