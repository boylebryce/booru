<?php

    $nav_html = 
    '
    <nav>
        <div id="nav-links">
            <a id="upload-link" class="nav-button" href="index.php">Upload</a>
            <a id="images-link" class="nav-button" href="images.php">Images</a>
        </div>
        <form id="search-form" method="GET" action="images.php">
            <input id="search-form-input" type="text" name="tags" placeholder="Search for tags here">
        </form>
        <form id="logout-form" method="POST" action="login.php">
            <input id="logout-form-submit" class="nav-button" type="submit" name="logout" type="submit" value="Logout">
        </form>
    </nav>
    ';

?>