<?php

    require_once $_SERVER['DOCUMENT_ROOT'] . '/booru/vendor/autoload.php';
    require_once $_SERVER['DOCUMENT_ROOT'] . '/booru/include/config.php';
    require_once $_SERVER['DOCUMENT_ROOT'] . '/booru/include/shared-html.php';

?>

<!DOCTYPE html>
<html lang="en">
    <?php if (isset($head_html)) echo $head_html; ?>
    <script async src="/booru/js/editor.js"></script>
    </head>
    <body>
        <?php if(isset($nav_html)) echo $nav_html; ?>
        <main>
            <img id="image-display"></img>
            <form id="add-tags-form">
                <label for="add-tags-input">Add tags</label>
                <input type="text" name="add-tags-input" id="add-tags-input" placeholder="Enter space-separated tags here">
            </form>
            <form id="delete-tags-form">
                <label>Current tags</label>
                <ul id="current-tags">
                </ul>
                <input id="delete-tags-submit" type="submit" value="Delete tags">
            </form>
            <button id="delete-image-button">Delete image</button>
        </main>
    </body>
</html>