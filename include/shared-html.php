<?php

    $head_html =
    '
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Booru</title>
        <meta name="description" content="A booru (tag-based image board) made from scratch by someone who doesn\'t know what they\'re doing. Expect things to either be only partially implemented or outright broken.">
        <link rel="stylesheet" href="/booru/css/style.css">
    ';

    $nav_html = 
    '
    <nav>
        <div id="nav-links">
            <a id="upload-link" class="button" href="/booru/index.php">Upload</a>
            <a id="images-link" class="button" href="/booru/images.php">Images</a>
        </div>
        <form id="search-form" method="GET" action="images.php">
            <input id="search-form-input" type="text" name="search" placeholder="Search for tags here">
        </form>
        <!--
        <form id="logout-form" method="POST" action="login.php">
            <input id="logout-form-submit" class="button" type="submit" name="logout" type="submit" value="Logout">
        </form>
        --!>
    </nav>
    ';

?>