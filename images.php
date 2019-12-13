<?php

    require('php/keys.php');

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
    
    $images = '';
    foreach ($result as $img) {
        $images .= '<img src="img/' . $img['img_path'] . '">';
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
        <?= $images ?>
        <a href="main.php">Back to main</a>
    </body>
</html>