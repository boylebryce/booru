<?php

    require('php/keys.php');

    $current_tags = '';

    // need to add new submitted tags
    if (isset($_POST['img_id']) && isset($_POST['tags'])) {
        try {
            $db = new PDO($dsn, $db_user, $db_pw);

            // check if any tags are new to tag system and add to tags table
            $tags = explode(' ', $_POST['tags']);
            foreach ($tags as $tag) {
                $query = 'SELECT COUNT(1) AS total FROM `tags` WHERE `tag_label` = :tag';
                $statement = $db->prepare($query);
                $statement->bindValue(':tag', $tag);
                $statement->execute();
                $tag_count = $statement->fetch();

                // tag doesn't exist in tags table, add it
                if ($tag_count['total'] == 0) {
                    $query = 'INSERT INTO `tags` (`tag_id`, `tag_label`, `tag_imgcount`) VALUES (NULL, :tag, 0);';
                    $statement = $db->prepare($query);
                    $statement->bindValue(':tag', $tag);
                    $result = $statement->execute();
                }
            }

            // add img_id, tag_id pairs to imagetags table
            foreach ($tags as $tag) {
                // get tag_id
                $query = 'SELECT `tag_id` FROM `tags` WHERE `tag_label` = :tag';
                $statement = $db->prepare($query);
                $statement->bindValue(':tag', $tag);
                $statement->execute();
                $tag_id = $statement->fetch()['tag_id'];

                // check that image doesn't already have tag before adding
                $query = 'SELECT COUNT(1) AS total FROM `imagetags` WHERE `imgID` = :img_id AND `tagID` = :tag_id';
                $statement = $db->prepare($query);
                $statement->bindValue(':img_id', $_POST['img_id']);
                $statement->bindValue('tag_id', $tag_id);
                $statement->execute();
                $result = $statement->fetch();

                // img, tag pair doesn't already exists, so add it
                if ($result['total'] == 0) {
                    $query = 'INSERT INTO `imagetags` (`ImageTagsID`, `imgID`, `tagID`) VALUES (NULL, :img_id, :tag_id);';
                    $statement = $db->prepare($query);
                    $statement->bindValue(':img_id', $_POST['img_id']);
                    $statement->bindValue(':tag_id', $tag_id);
                    $statement->execute();

                }
            }
            $statement->closeCursor();
        }
        catch (PDOException $e) {
            echo $e->getMessage();
        }
    }

    // get existing tags to display on page
    if (isset($_POST['img_id'])) {
        try {
            $db = new PDO($dsn, $db_user, $db_pw);
            $query = 'SELECT * FROM `imagetags` WHERE `imgID` = :img_id';
            $statement = $db->prepare($query);
            $statement->bindValue(':img_id', $_POST['img_id']);
            $statement->execute();
            $result = $statement->fetchAll();

            foreach ($result as $tag) {
                $query = 'SELECT * FROM `tags` WHERE `tag_id` = :tag_id';
                $statement = $db->prepare($query);
                $statement->bindValue(':tag_id', $tag['tagID']);
                $statement->execute();
                $label = $statement->fetch();
                $current_tags .= '<li>' . $label['tag_label'] . '</li>';
            }
            $statement->closeCursor();
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
        <img src="<?= 'img/' . $_POST['img_path'] ?>">
        <h2>Current tags:</h2>
        <ul>
            <?= $current_tags ?>
        </ul>
        <form method="POST" action="tag.php">
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