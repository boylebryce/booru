<?php

    require('php/keys.php');

    function random_filename($length, $directory = '', $extension = '') {
        $dir = $directory;
    
        do {
            $key = '';
            $keys = array_merge(range(0, 9), range('a', 'z'));
    
            for ($i = 0; $i < $length; $i++) {
                $key .= $keys[array_rand($keys)];
            }
        } while (file_exists($dir . '/' . $key . (!empty($extension) ? '.' . $extension : '')));
    
        return $key . (!empty($extension) ? '.' . $extension : '');
    }

    $preview = '';
    $img_path = '';
    $img_id = '';

    if (isset($_POST['imageData'])) {
        $data = $_POST['imageData'];
        if (preg_match('/^data:image\/(\w+);base64,/', $data, $type)) {
            $data = substr($data, strpos($data, ',') + 1);
            $type = strtolower($type[1]); // jpg, png, gif
        
            if (!in_array($type, [ 'jpg', 'jpeg', 'gif', 'png' ])) {
                throw new \Exception('invalid image type');
            }
        
            $data = base64_decode($data);
        }
        else {
            throw new \Exception('did not match data URI with image data');
        }
        
        $img_path = random_filename(16, 'img', $type);
        file_put_contents('img/' . $img_path, $data);

        try {
            $db = new PDO($dsn, $db_user, $db_pw);
            $query = 'INSERT INTO `images` (`img_id`, `img_path`, `img_tagcount`) VALUES (NULL, :img_path, 0);';
            $statement = $db->prepare($query);
            $statement->bindValue(':img_path', $img_path);
            $result = $statement->execute();
            $img_id = $db->lastInsertId();

            $statement->closeCursor();
        }
        catch (PDOException $e) {
            echo $e->getMessage();
        }
        $preview = '<img src="img/' . $img_path . '">';
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
        <?= $preview ?>
        <a href="main.php">Back to main</a>
        <a href="images.php">View uploaded images</a>
        <?php if ($img_id !== '') { ?>
        <form method="POST" action="tag.php">
            <input type="submit" value="Tag this image">
            <input type="text" value="<?= $img_id ?>" name="img_id" style="display:none">
            <input type="text" value="<?= $img_path ?>" name="img_path" style="display:none">
        </form>
        <?php } ?>
    </body>
</html>