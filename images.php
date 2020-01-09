<?php

    require_once $_SERVER['DOCUMENT_ROOT'] . '/booru/php/paths.php';
    require_once WEB_ROOT . '/booru/php/session.php';
    require_once WEB_ROOT . '/booru/php/shared_html.php';

?>

<!DOCTYPE html>
<html>
    <?php if (isset($head_html)) echo $head_html; ?>
        <script async src='/booru/js/images.js'></script>
        <script>
            const GET = <?php echo json_encode($_GET); ?>;
        </script>
    </head>
    <body>
        <?php if(isset($nav_html)) echo $nav_html; ?>
        <main>
            <div id="images-container"></div>
        </main>
    </body>
</html>