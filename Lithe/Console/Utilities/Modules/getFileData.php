<?php

function getFileData(string $url)
{
    $options = [
        'http' => [
            'header' => "User-Agent: PHP\r\n",
            'method' => 'GET',
        ],
    ];

    $context = stream_context_create($options);
    $response = @file_get_contents($url, false, $context);

    if ($response === FALSE) {
        $error = error_get_last()['message'] ?? 'Unknown error';
        return false;
    }

    $data = json_decode($response, true);

    if (json_last_error() !== JSON_ERROR_NONE) {
        return false;
    }

    if (empty($data)) {
        echo "No data found at URL: $url\n"; // Print a specific error if no data is found
        return false;
    }

    return $data;
}
