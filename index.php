<?php

    require_once $_SERVER['DOCUMENT_ROOT'] . '/booru/include/shared_html.php';

?>

<!DOCTYPE html>
<html>
    <?php if (isset($head_html)) echo $head_html; ?>
        <link rel="stylesheet" href="/booru/css/index.css">
        <script async src='/booru/js/index.js'></script>
    </head>
    <body>
        <?php if(isset($nav_html)) echo $nav_html; ?>
        <main>
            <div id="paste-preview-area" style="display:none">
                    <img id="paste-preview">
                </div>
                <h2 id="paste-instructions">Paste an image in the browser or choose an image from your system</h2>
                <p id="file-name"></p>
                <form id="upload-form" enctype="multipart/form-data">
                    <label id="upload-button" class="button" for="upload-input">Choose a file</label>
                    <input id="upload-input" type="file" name="userfile" required>
                    <input id="upload-submit" class="button" type="submit" value="Upload File" tabindex="1" style="display:none">
                </form>
        </main>
    </body>
</html>