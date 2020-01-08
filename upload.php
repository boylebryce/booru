<?php

    require_once 'php/paths.php';
    require_once CONFIG_FILE;
    require_once 'php/session.php';
    require_once 'php/functions.php';
    require_once 'php/shared_html.php';

    if (isset($_FILES['userfile'])) {
        $uploaddir = 'img/';
        $uploadext = explode('/', $_FILES['userfile']['type'])[1];
        $filename = random_filename(16, $uploaddir, $uploadext);
        $uploadfile = $uploaddir . $filename;
        
        if (move_uploaded_file($_FILES['userfile']['tmp_name'], $uploadfile)) {
            try {
                $db = new PDO($dsn, $db_user, $db_pw);
                $query = 'INSERT INTO `images` (`img_id`, `img_path`, `img_tagcount`) VALUES (NULL, :img_path, 0);';
                $statement = $db->prepare($query);
                $statement->bindValue(':img_path', $filename);
                $result = $statement->execute();
                $img_id = $db->lastInsertId();
                $statement->closeCursor();

                // send newly uploaded image to editor
                header('Location: /booru/editor.php?img_id=' . $img_id);
                exit();
            }
            catch (PDOException $e) {
                echo $e->getMessage();
            }
        }
    }

?>

<!doctype html>
<html lang="en">
    <?php if(isset($head_html)) echo $head_html; ?>
    </head>
    <body>
        <?php if (isset($nav_html)) echo $nav_html; ?>
        <main>
            <?php if (isset($preview)) echo $preview; ?>
            <?php if (isset($img_id)) { ?>
                <form method="POST" action="editor.php">
                    <input type="submit" value="Tag this image">
                    <input type="hidden" value="<?= $img_id ?>" name="img_id">
                </form>
            <?php } ?>
        </main>
    </body>
</html>