<?php

    require_once $_SERVER['DOCUMENT_ROOT'] . '/booru/vendor/autoload.php';
    require_once $_SERVER['DOCUMENT_ROOT'] . '/booru/include/config.php';
    require_once $_SERVER['DOCUMENT_ROOT'] . '/booru/include/whitelist.php';

    use GuzzleHttp\Client;

    if (!(isset($_POST['img_id']) && isset($_POST['tags']))) {
        echo json_encode(['error' => 'Invalid request']);
        exit();
    }

    $api_client = new Client([
        'base_uri' => API_BASE_URI,
    ]);

    $response = $api_client->request('POST', 'booru-api/add_tags.php', [
        'form_params' => [
            'img_id' => $_POST['img_id'],
            'tags' => $_POST['tags'],
        ]
    ]);

    echo $response->getBody();

?>