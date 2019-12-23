<?php

    require_once('php/session.php');
    require_once('php/shared_html.php');

?>

<!doctype html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Booru</title>
        <meta name="description" content="A booru (tag-based image board) made from scratch by someone who doesn't know what they're doing. Expect things to either be only partially implemented or outright broken.">
        <link rel="stylesheet" href="css/style.css">
        <link rel="stylesheet" href="css/index.css">
        <script src="js/scripts.js"></script>
    </head>
    <body>
        <?php if(isset($nav_html)) echo $nav_html; ?>
        <main>
            <div id="paste-preview-area" style="display:none">
                <img id="paste-preview">
            </div>
            <form id="upload-form" method="POST" action="upload.php" enctype="multipart/form-data">
                <input type="file" name="userfile" required>
                <input id="upload-form-submit" type="submit" value="Upload File" tabindex="1">
            </form>
        </main>
    </body>
</html>