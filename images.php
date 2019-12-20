<?php

    require_once('php/keys.php');
    require_once('php/login.php');

    // display images with tags in search
    if (isset($_GET['search'])) {
        try {
            $db = new PDO($dsn, $db_user, $db_pw);

            // separate tags by space-delimiter
            $search_tags = explode(' ', $_GET['search']);
            $tags = array(); // tag_id => tag_label
            $images = array(); // img_id => img_path
            $image_ids = array();

            // get tag_id for each tag
            foreach ($search_tags as $tag_label) {
                $query = 'SELECT * FROM `tags` WHERE `tag_label` = :tag_label';
                $statement = $db->prepare($query);
                $statement->bindValue(':tag_label', $tag_label);
                $statement->execute();
                $tags[$statement->fetch()['tag_id']] = $tag_label;
            }

            // get img_id for every image that match first tag_id
            $first_tag_id = array_key_first($tags);
            $query = 'SELECT `img_id` FROM `imagetags` WHERE `tag_id` = :tag_id';
            $statement = $db->prepare($query);
            $statement->bindValue(':tag_id', $first_tag_id);
            $statement->execute();
            $result = $statement->fetchAll();
            $image_ids = array();
            foreach ($result as $img) {
                $image_ids[] = $img['img_id'];
            }

            // get img_id for every image that matches tag_id
            foreach ($tags as $tag_id => $tag_label) {
                $eligible = array();
                $query = 'SELECT `img_id` FROM `imagetags` WHERE `tag_id` = :tag_id';
                $statement = $db->prepare($query);
                $statement->bindValue(':tag_id', $tag_id);
                $statement->execute();
                $result = $statement->fetchAll();
                foreach ($result as $img) {
                    if (array_search($img['img_id'], $image_ids) !== false) {
                        $eligible[] = $img['img_id'];
                    }
                }
                $image_ids = $eligible;
            }

            // populate images array with img_path
            foreach ($image_ids as $img_id) {
                $query = 'SELECT `img_path` FROM `images` WHERE `img_id` = :img_id';
                $statement = $db->prepare($query);
                $statement->bindValue(':img_id', $img_id);
                $statement->execute();
                $images[$img_id] = $statement->fetch()['img_path'];
            }
            $statement->closeCursor();

            $image_display = '<h2>Showing images with tag(s): ';
            foreach ($tags as $tag_id => $tag_label) {
                $image_display .= $tag_label . ' ';
            }
            $image_display .= '<div>';
            foreach ($images as $img_id => $img_path) {
                $image_display .= '<a href="editor.php?img_id=' . $img_id . '"><img src="img/' . $img_path . '"></a>';
            }
            $image_display .= '</div>';
        }
        catch (PDOException $e) {
            echo $e->getMessage();
        }
    }

    // display all images
    else {
        try {
            $db = new PDO($dsn, $db_user, $db_pw);
            $query = 'SELECT * FROM `images`';
            $statement = $db->prepare($query);
            $statement->execute();
            $result = $statement->fetchAll();
            $statement->closeCursor();
        }
        catch (PDOException $e) {
            echo $e->getMessage();
        }
        
        $image_display = '';
        foreach ($result as $img) {
            $image_display .= '<a href="editor.php?img_id=' . $img['img_id'] . '"><img src="img/' . $img['img_path'] . '"></a>';
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
        <link rel="stylesheet" href="css/images.css">
    </head>

    <body>
    <?php if (isset($_SESSION['user'])) { ?>
        <header>
            <a id="upload-link" href="index.php">Upload</a>
            <form id="search-form" method="GET" action="images.php">
                <input id="search-form-input" type="text" name="search">
            </form>
        </header>
        <main>
            <h2>Click an image to tag it</h2>
            <div>
                <?= $image_display ?>
            </div>
        </main>
    <?php } else { echo $login_form; } ?>
    </body>
</html>