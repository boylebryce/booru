<?php

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
        <img id="pastedImage" src="">
        <div id="imageActions">
            <form id="submitImageForm" method="POST" action="upload.php">
                <input type="submit" value="Upload">
                <input type="text" id="imageData" name="imageData" style="display:none">
            </form>
            <button id="resetImage">Reset</button>
        </div>
        <a href="images.php">View uploaded images</a>
    </body>
</html>