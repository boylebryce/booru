<?php

    require_once('php/session.php');
    require_once('php/shared_html.php');

?>

<!doctype html>
<html lang="en">
    <?php if(isset($head_html)) echo $head_html; ?>
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