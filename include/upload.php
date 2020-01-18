<?php

    require_once $_SERVER['DOCUMENT_ROOT'] . '/booru/vendor/autoload.php';
    require_once $_SERVER['DOCUMENT_ROOT'] . '/booru/include/config.php';

    use GuzzleHttp\Client;

    // TODO: Verify that request is coming from booru page
    // Consider using PHP sessions and nonces

    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['userfile'])) {
        $api_client = new Client([
            'base_uri' => API_BASE_URI,
        ]);

        // Resave uploaded image with correct extension to preserve
        // image metadata when sending to backend
        $ext = explode('/', $_FILES['userfile']['type'])[1];
        $temp = explode('.', $_FILES['userfile']['tmp_name'])[0] . '.' . $ext;
        move_uploaded_file($_FILES['userfile']['tmp_name'], $temp);

        $response = $api_client->request('POST', 'booru-api/upload.php', [
            'multipart' => [
                [
                    'name'      => 'userfile',
                    'contents'  => fopen($temp, 'r'),
                ]
            ]
        ]);

        echo $response->getBody();
        unlink($temp);
    }
    else if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        echo json_encode(['error' => 'Invalid request: userfile missing']);
    }
    else {
        header('Location: /booru');
        exit();
    }

?>