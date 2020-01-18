<?php

    require_once $_SERVER['DOCUMENT_ROOT'] . '/booru/vendor/autoload.php';
    require_once $_SERVER['DOCUMENT_ROOT'] . '/booru/include/config.php';

    use GuzzleHttp\Client;

    if (!isset($_GET['img_id'])) {
        header('Location: /booru');
        exit();
    }

    $api_client = new Client([
        'base_uri' => API_BASE_URI,
    ]);

    $img_id = $_GET['img_id'];
    $response = $api_client->request('GET', 'booru-api/image.php?img_id=' . $img_id);
    $image = json_decode($response->getBody(), true);

?>