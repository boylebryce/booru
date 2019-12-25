<?php

    require_once('php/keys.php');
    require_once('php/session.php');
    require_once('php/shared_html.php');

    // display images with tags in search
    if (isset($_GET['search'])) {
        try {
            $db = new PDO($dsn, $db_user, $db_pw);

            // separate tags by space-delimiter
            $raw_tags = explode(' ', $_GET['search']);
            $search_tags = array();
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
                            $search_tags[] = $raw_tag;
                        }
                    }
                    // raw_tag is part of a quote-enclosed tag
                    else {
                        $quote_tag .= ' ' . $raw_tag;

                        // raw_tag is closing the quote-enclosed tag
                        if (substr($raw_tag, -1) === '"') {
                            $quote_tag = trim($quote_tag, '"');
                            $search_tags[] = $quote_tag;
                            $quote_tag = '';
                        }
                    }
                }
            }

            $tags = array(); // tag_id => tag_label
            $images = array(); // img_id => img_path
            $image_ids = array();
            
            // 

            // get tag_id for each tag
            foreach ($search_tags as $tag_label) {
                $query = 'SELECT `tag_id` FROM `tags` WHERE `tag_label` = :tag_label';
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
    <?php if(isset($head_html)) echo $head_html; ?>
    </head>
    <body>
        <?php if (isset($nav_html)) echo $nav_html; ?>
        <main>
            <h2>Click an image to tag it</h2>
            <div>
                <?= $image_display ?>
            </div>
        </main>
    </body>
</html>