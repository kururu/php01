<?php
$rssUrl = "https://note.com/kururu01/m/m417488290466/rss";

// RSSフィードを取得
$rssContent = @file_get_contents($rssUrl);
if ($rssContent === false) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'RSS読み込みに失敗しました']);
    exit;
}

// XMLを読み込む
$xml = simplexml_load_string($rssContent);
if (!$xml || !isset($xml->channel->item[0])) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'RSSの構造が不正です']);
    exit;
}

$item = $xml->channel->item[0];

// 名前空間を取得（media, note用）
$namespaces = $item->getNamespaces(true);

// media:thumbnail
$thumbnail = null;
if (isset($namespaces['media'])) {
    $media = $item->children($namespaces['media']);
    if (isset($media->thumbnail)) {
        $thumbnail = (string)$media->thumbnail;
    }
}

// note:creatorImage と note:creatorName
$creatorImage = null;
$creatorName = null;
if (isset($namespaces['note'])) {
    $note = $item->children($namespaces['note']);
    if (isset($note->creatorImage)) {
        $creatorImage = (string)$note->creatorImage;
    }
    if (isset($note->creatorName)) {
        $creatorName = (string)$note->creatorName;
    }
}

// 結果を整形
$data = [
    'title'         => (string)$item->title,
    'thumbnail'     => $thumbnail,
    'description'   => strip_tags((string)$item->description),
    'creatorImage'  => $creatorImage,
    'creatorName'   => $creatorName,
    'pubDate'       => (string)$item->pubDate,
    'link'          => (string)$item->link,
    'guid'          => (string)$item->guid
];

// JSON出力
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: https://kururu.github.io');
echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
?>