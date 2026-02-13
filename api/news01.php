<?php
$apiKey = getenv('NEWS_API');
if (!$apiKey) {
    http_response_code(500);
    exit('API key not set');
}

// NewsAPI（BBCの記事を含む例）
$url = 'https://newsapi.org/v2/top-headlines?' . http_build_query([
    'sources' => 'bbc-news',
    'apiKey'  => $apiKey,
]);

$ch = curl_init($url);
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_TIMEOUT => 5,
]);

$response = curl_exec($ch);
curl_close($ch);

$data = json_decode($response, true);

if (!isset($data['articles'])) {
    http_response_code(500);
    exit('Invalid API response');
}

// 必要な項目だけ抽出
$result = [];
foreach ($data['articles'] as $article) {
    $result[] = [
        'title' => $article['title'] ?? '',
        'thumbnail' => $article['urlToImage'] ?? null,
    ];
}

// JSONで返す（スマホガジェット向け）
header('Content-Type: application/json; charset=utf-8');
echo json_encode($result, JSON_UNESCAPED_UNICODE);
?>