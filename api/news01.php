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

$data = file_get_contents($url, false, $context);
$article = $data['articles'][0] ?? null;

if (!$article) {
    echo json_encode(['error' => 'no articles'], JSON_UNESCAPED_UNICODE);
    exit;
}

$output = [
    'title'     => $article['title'] ?? '',
    'thumbnail' => $article['urlToImage'] ?? '',
];

header('Content-Type: application/json; charset=utf-8');
echo json_encode($output, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
?>