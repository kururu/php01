<?php
$rss_url = "https://note.com/kururu01/m/m417488290466/rss";
$rss_content = @file_get_contents($rss_url);

if ($rss_content === false) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'RSS取得に失敗しました']);
    exit;
}

$xml = simplexml_load_string($rss_content);

if (!$xml || !isset($xml->channel->item[0])) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'RSSの構造が不正です']);
    exit;
}

// media名前空間の登録
$namespaces = $xml->channel->item[0]->getNamespaces(true);
$media = $xml->channel->item[0]->children($namespaces['media'] ?? '');

$thumbnailUrl = '';
if (isset($media->thumbnail)) {
    $thumbnailUrl = (string)$media->thumbnail->attributes()->url;
}

$latest = $xml->channel->item[0];

$data = [
    'title'       => (string)$latest->title,
    'link'        => (string)$latest->link,
    'description' => (string)$latest->description,
    'pubDate'     => (string)$latest->pubDate,
    'thumbnail'   => $thumbnailUrl
];

header('Content-Type: application/json; charset=utf-8');
echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
?>