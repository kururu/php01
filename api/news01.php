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
$data = json_decode($response, true);

if (!isset($data['articles'][0])) {
    echo json_encode(['error' => 'no articles', 'debug' => $data]);
    exit;
}

$article = $data['articles'][0];

echo json_encode([
    'title'     => $article['title'],
    'thumbnail' => $article['urlToImage']
], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
?>