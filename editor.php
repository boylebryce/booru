<?php

    require_once $_SERVER['DOCUMENT_ROOT'] . '/booru/include/config.php';
    require_once $_SERVER['DOCUMENT_ROOT'] . '/booru/php/session.php';
    require_once $_SERVER['DOCUMENT_ROOT'] . '/booru/php/functions.php';
    require_once $_SERVER['DOCUMENT_ROOT'] . '/booru/php/shared_html.php';

    $current_tags = '';
    $db_error = '';

    /* To do:
        High priority:
        * Add error handling, especially to deletions

        Low priority:
        * How to handle no image argument - redirect to index or images?
    */

    /* Script Control flow:
        if POST[add_tags] and POST[img_id]          -> add tags to image
        if POST[delete_tags] and POST[img_id]       -> delete tags from image
        if POST[delete_img] and POST[img_id]        -> delete image
        if POST[img_id] or GET[img_id]              -> display image and tags
    */

    // img_id must be set to do anything in the editor
    if(isset($_POST['img_id']) || isset($_GET['img_id'])) {
        $img_id = isset($_POST['img_id']) ? $_POST['img_id'] : $_GET['img_id'];
        $db = new PDO(DSN, DB_USER, DB_PW);

        // validate img_id - must exist in images table
        try {
            $query = 'SELECT COUNT(*) FROM `images` WHERE `img_id` = :img_id';
            $statement = $db->prepare($query);
            $statement->bindValue(':img_id', $img_id);
            $statement->execute();

            // img_id doesn't exist
            if ($statement->fetchColumn() == 0) {
                $db_error .= 'Error: img_id ' . $img_id . ' doesn\'t exist.';
            }
        }
        catch (PDOException $e) {
            $db_error .= $e->getMessage();
        }

        // add tags to image
        if (isset($_POST['add_tags']) && $db_error === '') {
            try {
                $db = new PDO(DSN, DB_USER, DB_PW);

                /*  
                    tags are passed as a space-separated string of individual tags
                    tags can contain spaces if they are enclosed in "double quotes"
                */

                $raw_tags = explode(' ', $_POST['add_tags']);
                $tags = array();
                $quote_tag = '';

                foreach ($raw_tags as $raw_tag) {
                    if ($raw_tag !== '') {
                        if ($quote_tag === '') {
                            // raw_tag is the start of a quote-enclosed tag
                            if ($raw_tag[0] === '"') {
                                $quote_tag .= $raw_tag;
                            }
                            // raw_tag is a standard tag
                            else {
                                $tags[] = $raw_tag;
                            }
                        }
                        // raw_tag is part of a quote-enclosed tag
                        else {
                            $quote_tag .= ' ' . $raw_tag;

                            // raw_tag is closing the quote-enclosed tag
                            if (substr($raw_tag, -1) === '"') {
                                $quote_tag = trim($quote_tag, '"');
                                $tags[] = $quote_tag;
                                $quote_tag = '';
                            }
                        }
                    }
                }

                if ($quote_tag !== '') {
                    $tag_error = 'Tagging error: no closing quote for ' . $quote_tag;
                }

                // check if any tags are new to tag system and add to tags table
                foreach ($tags as $tag_label) {
                    $query = 'SELECT COUNT(*) FROM `tags` WHERE `tag_label` = :tag';
                    $statement = $db->prepare($query);
                    $statement->bindValue(':tag', $tag_label);
                    $statement->execute();

                    // tag doesn't exist in tags table, add it
                    if ($statement->fetchColumn() == 0) {
                        $query = 'INSERT INTO `tags` (`tag_id`, `tag_label`, `tag_imgcount`) VALUES (NULL, :tag_label, 0);';
                        $statement = $db->prepare($query);
                        $statement->bindValue(':tag_label', $tag_label);
                        $statement->execute();
                    }
                }

                // add img_id and tag_id to imagetags table
                foreach ($tags as $tag_label) {
                    // get tag_id
                    $query = 'SELECT `tag_id` FROM `tags` WHERE `tag_label` = :tag_label';
                    $statement = $db->prepare($query);
                    $statement->bindValue(':tag_label', $tag_label);
                    $statement->execute();
                    $tag_id = $statement->fetch()['tag_id'];

                    // check that image doesn't already have tag before adding
                    $query = 'SELECT COUNT(*) FROM `imagetags` WHERE `img_id` = :img_id AND `tag_id` = :tag_id';
                    $statement = $db->prepare($query);
                    $statement->bindValue(':img_id', $img_id);
                    $statement->bindValue('tag_id', $tag_id);
                    $statement->execute();

                    // img, tag pair doesn't already exists, so add it
                    if ($statement->fetchColumn() == 0) {
                        $query = 'INSERT INTO `imagetags` (`imagetags_id`, `img_id`, `tag_id`) VALUES (NULL, :img_id, :tag_id);';
                        $statement = $db->prepare($query);
                        $statement->bindValue(':img_id', $img_id);
                        $statement->bindValue(':tag_id', $tag_id);
                        $statement->execute();
                    }
                }
                $statement->closeCursor();
            }
            catch (PDOException $e) {
                $db_error .= $e->getMessage();
            }
        }

        // delete tags from image
        if (isset($_POST['delete_tags']) && $db_error === '') {
            foreach ($_POST['delete_tags'] as $tag_id) {
                try {
                    $db = new PDO(DSN, DB_USER, DB_PW);
                    $query = 'DELETE FROM `imagetags` WHERE `img_id` = :img_id AND `tag_id` = :tag_id';
                    $statement = $db->prepare($query);
                    $statement->bindValue(':img_id', $img_id);
                    $statement->bindValue(':tag_id', $tag_id);
                    $statement->execute();
                }
                catch (PDOException $e) {
                    $db_error .= $e->getMessage();
                }
            }
        }

        // delete image
        if (isset($_POST['delete_image']) && $db_error === '') {
            try {
                $db = new PDO(DSN, DB_USER, DB_PW);

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
            }
            catch (PDOException $e) {
                $db_error .= $e->getMessage();
            }
        }

        // display image and tags
        if ((isset($_POST['img_id']) || isset($_GET['img_id'])) && $db_error === '') {
            $img_id = isset($_POST['img_id']) ? $_POST['img_id'] : $_GET['img_id'];
            $img_path = isset($_POST['img_path']) ? $_POST['img_path'] : '';
            try {
                $db = new PDO(DSN, DB_USER, DB_PW);

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

                    // add quotes to multiword tags
                    if (strstr($label['tag_label'], ' ')) {
                        $multiword_label = '&quot;' . $label['tag_label'] . '&quot;';
                    }
                    else {
                        $multiword_label = '';
                    }

                    $current_tags .= '<a href="images.php?search=';
                    $current_tags .= $multiword_label !== '' ? $multiword_label : $label['tag_label'];
                    $current_tags .= '"><li><input type="checkbox" name="delete_tags[]" value="' . $label['tag_id'] . '">' . $label['tag_label'] . '</li></a>';
                }
                $statement->closeCursor();
            }
            catch (PDOException $e) {
                $db_error .= $e->getMessage();
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
            <!-- Show database error -->
            <?php if ($db_error !== '') { echo $db_error; } else { ?>
                <!-- Show image, tags, and editor -->
                <?php if (isset($img_id)) { ?>
                    <img src="<?= 'img/' . $img_path ?>">
                    <?php if (isset($tag_error)) echo '<p>' . $tag_error . '</p>'; ?>
                    <form id="add-tags-form" method="POST" action="editor.php">
                        <label>Add space-separated tags here</label>
                        <input type="text" name="add_tags" required>
                        <input type="hidden" name="img_id" value="<?= $img_id ?>">
                        <input type="hidden" name="img_path" value="<?= $img_path ?>">
                        <input type="submit" value="Submit" name="add_tags_form">
                    </form>

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
                    <p>Something went wrong: img_id is not set.</p>
                <?php } ?>
            <?php } ?>
        </main>
    </body>
</html>