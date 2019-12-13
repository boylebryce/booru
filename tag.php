<?php

    require('php/keys.php');

    if (isset($_POST['img_id']) && isset($_POST['tags'])) {
        try {
            $db = new PDO($dsn, $db_user, $db_pw);

            // check if any tags are new and add to tags table
            $tags = explode(' ', $_POST['tags']);
            foreach ($tags as $tag) {
                $query = 'SELECT COUNT(1) AS total FROM `tags` WHERE `tag_label` = :tag';
                $statement = $db->prepare($query);
                $statement->bindValue(':tag', $tag);
                $statement->execute();
                $tag_count = $statement->fetch();

                // if tag doesn't exist in tags table, add it
                if ($tag_count['total'] == 0) {
                    $query = 'INSERT INTO `tags` (`tag_id`, `tag_label`, `tag_imgcount`) VALUES (NULL, :tag, 0);';
                    $statement = $db->prepare($query);
                    $statement->bindValue(':tag', $tag);
                    $result = $statement->execute();
                }
            }

            // add img_id, tag_id pairs to imagetags table
        }
        catch (PDOException $e) {
            echo $e->getMessage();
        }
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
        <script src="js/scripts.js"></script>
    </head>
    <body>
        <?php if (isset($_POST['img_id'])) { ?>
        <form method="POST" action="tag.php">
            <img src="<?= 'img/' . $_POST['img_path'] ?>">
            <label>Add space-separated tags here</label>
            <input type="text" name="tags">
            <input type="text" name="img_id" value="<?= $_POST['img_id'] ?>" style="display:none">
            <input type="text" name="img_path" value="<?= $_POST['img_path'] ?>" style="display:none">
            <input type="submit" value="Submit">
        </form>
        <?php } ?>
        <a href="main.php">Back to main</a>
        <a href="images.php">View uploaded images</a>
    </body>
</html>