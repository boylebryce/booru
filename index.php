<?php

    require_once('php/login.php');

?>

<!doctype html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Booru</title>
        <meta name="description" content="A booru (tag-based image board) made from scratch by someone who doesn't know what they're doing. Expect things to either be only partially implemented or outright broken.">
        <link rel="stylesheet" href="css/style.css">
        <script src="js/scripts.js"></script>
    </head>
    <body>
    <?php if (isset($_SESSION['user'])) { ?>
        <div id="paste-preview-area" style="display:none">
            <img id="paste-preview">
        </div>
        <form id="upload-form" method="POST" action="upload.php" enctype="multipart/form-data">
            <input type="file" name="userfile" required>
            <input id="upload-form-submit" type="submit" value="Upload File" tabindex="1">
        </form>
        <form method="GET" action="images.php">
            <label for="search">Search for tags</label>
            <input type="text" name="search">
        </form>
        <a href="images.php">View uploaded images</a>
    <?php } else { echo $login_form; } ?>
    </body>
</html>