<?php

    require_once('php/keys.php');
    require_once('php/session.php');
    require_once('php/functions.php');
    require_once('php/shared_html.php');

    $current_tags = '';

    /* To do:
        High priority:
        * Handle img_id doesn't exist - consider restructuring control flow to check for this first
        * Add error handling, especially to deletions

        Low priority:
        * Consider changing all POST[img_id] to GET[img_id] by altering form action url?
    */

    /* Script Control flow:
        if POST[add_tags] and POST[img_id]          -> add tags to image
        if POST[delete_tags] and POST[img_id]       -> delete tags from image
        if POST[delete_img] and POST[img_id]        -> delete image
        if POST[img_id] or GET[img_id]              -> display image and tags
    */

    // add tags to image
    if (isset($_POST['add_tags']) && isset($_POST['img_id'])) {
        $img_id = $_POST['img_id'];
        try {
            $db = new PDO($dsn, $db_user, $db_pw);

            // check if any tags are new to tag system and add to tags table
            $tags = explode(' ', $_POST['add_tags']);
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
                $query = 'SELECT COUNT(1) AS total FROM `imagetags` WHERE `img_id` = :img_id AND `tag_id` = :tag_id';
                $statement = $db->prepare($query);
                $statement->bindValue(':img_id', $_POST['img_id']);
                $statement->bindValue('tag_id', $tag_id);
                $statement->execute();
                $result = $statement->fetch();

                // img, tag pair doesn't already exists, so add it
                if ($result['total'] == 0) {
                    $query = 'INSERT INTO `imagetags` (`imagetags_id`, `img_id`, `tag_id`) VALUES (NULL, :img_id, :tag_id);';
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

    // delete tags from image
    if (isset($_POST['delete_tags']) && isset($_POST['img_id'])) {
        foreach ($_POST['delete_tags'] as $tag) {
            try {
                $db = new PDO($dsn, $db_user, $db_pw);
                $query = 'DELETE FROM `imagetags` WHERE `img_id` = :img_id AND `tag_id` = :tag_id';
                $statement = $db->prepare($query);
                $statement->bindValue(':img_id', $_POST['img_id']);
                $statement->bindValue(':tag_id', $tag);
                $statement->execute();
            }
            catch (PDOException $e) {
                echo $e->getMessage();
            }
        }
    }

    // delete image
    /*
        To do:
            * Remove all entries in imagetags table
            * Delete image file on server
            * Remove entry from images table
    */
    if (isset($_POST['delete_image']) && isset($_POST['img_id'])) {
        $img_id = $_POST['img_id'];
        $deletion_result = '';

        try {
            $db = new PDO($dsn, $db_user, $db_pw);

            // Remove all entries in imagetags table
            $query = 'DELETE FROM `imagetags` WHERE `img_id` = :img_id';
            $statement = $db->prepare($query);
            $statement->bindValue(':img_id', $img_id);
            $statement->execute();

            // Delete image file on server
            $query = 'SELECT `img_path` FROM `images` WHERE `img_id` = :img_id';
            $statement = $db->prepare($query);
            $statement->bindValue(':img_id', $img_id);
            $statement->execute();
            $img_path = $statement->fetch()['img_path'];
            unlink('img/' . $img_path);

            // Remove entry from images table
            $query = 'DELETE FROM `images` WHERE `img_id` = :img_id';
            $statement = $db->prepare($query);
            $statement->bindValue(':img_id', $img_id);
            $statement->execute();
            
            $statement->closeCursor();
            $deletion_result = 'Image successfully deleted.';
        }
        catch (PDOException $e) {
            echo $e->getMessage();
        }
    }

    // display image and tags
    if (isset($_POST['img_id']) || isset($_GET['img_id'])) {
        $img_id = isset($_POST['img_id']) ? $_POST['img_id'] : $_GET['img_id'];
        $img_path = isset($_POST['img_path']) ? $_POST['img_path'] : '';
        try {
            $db = new PDO($dsn, $db_user, $db_pw);

            // get img_path if not already set
            if ($img_path === '') {
                $query = 'SELECT * FROM `images` WHERE `img_id` = :img_id';
                $statement = $db->prepare($query);
                $statement->bindValue(':img_id', $img_id);
                $statement->execute();
                $img_path = $statement->fetch()['img_path'];
            }

            // get existing tags
            $query = 'SELECT * FROM `imagetags` WHERE `img_id` = :img_id';
            $statement = $db->prepare($query);
            $statement->bindValue(':img_id', $img_id);
            $statement->execute();
            $result = $statement->fetchAll();

            foreach ($result as $tag) {
                $query = 'SELECT * FROM `tags` WHERE `tag_id` = :tag_id';
                $statement = $db->prepare($query);
                $statement->bindValue(':tag_id', $tag['tag_id']);
                $statement->execute();
                $label = $statement->fetch();
                $current_tags .= '<a href="images.php?search=' . $label['tag_label'] . '"><li><input type="checkbox" name="delete_tags[]" value="' . $label['tag_id'] . '">' . $label['tag_label'] . '</li></a>';
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
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Booru</title>
        <meta name="description" content="A booru (tag-based image board) made from scratch by someone who doesn't know what they're doing. Expect things to either be only partially implemented or outright broken.">
        <link rel="stylesheet" href="css/style.css">
        
    </head>
    <body>
        <?php if (isset($nav_html)) echo $nav_html; ?>

        <!-- Show image, tags, and editor -->
        <?php if (isset($img_id)) { ?>
            <?php if (isset($deletion_result)) {echo '<p>' . $deletion_result . '</p>';} ?>
            <img src="<?= 'img/' . $img_path ?>">
            <form id="add-tags-form" method="POST" action="editor.php">
                <label>Add space-separated tags here</label>
                <input type="text" name="add_tags" required>
                <input type="hidden" name="img_id" value="<?= $img_id ?>">
                <input type="hidden" name="img_path" value="<?= $img_path ?>">
                <input type="submit" value="Submit" name="add_tags_form">
            </form>
            <div>
                <a href="index.php">Back to main</a>
                <a href="images.php">View uploaded images</a>
            </div>
            <h2>Current tags:</h2>
            <form id="tag-deletion-form" method="POST" action="editor.php">
                <ul>
                    <?= $current_tags ?>
                </ul>
                <input type="hidden" name="img_id" value="<?= $img_id ?>">
                <input type="hidden" name="img_path" value="<?= $img_path ?>">
                <input type="submit" value="Delete selected tags">
            </form>
            <form id="image-deletion-form" method="POST" action="editor.php">
                <input type="submit" value="Delete image" name="delete_image">
                <input type="hidden" name="img_id" value="<?= $img_id ?>">
            </form>
        <!-- No image argument -->
        <?php } else { ?>
            
        <?php } ?>
    </body>
</html>