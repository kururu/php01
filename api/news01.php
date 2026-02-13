<?php
$apiKey = getenv('NEWS_API');

$url = 'https://newsapi.org/v2/top-headlines?' . http_build_query([
    'sources' => 'bbc-news',
    'apiKey'  => $apiKey,
]);

$response = file_get_contents($url);
$data = json_decode($response, true);

var_dump($data);
?>