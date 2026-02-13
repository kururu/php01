<?php
$apiKey = getenv('NEWS_API');
if (!$apiKey) {
    die('API KEY NOT FOUND');
}

$url = 'https://newsapi.org/v2/top-headlines?' . http_build_query([
    'sources' => 'bbc-news',
    'apiKey'  => $apiKey,
]);

$context = stream_context_create([
    'http' => [
        'ignore_errors' => true
    ]
]);

$response = file_get_contents($url, false, $context);
var_dump($http_response_header);
echo $response;
?>