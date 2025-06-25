<?php
// RSSフィードのURL
$rss_url = "https://note.com/kururu01/m/m417488290466/rss";

// RSSを取得
$rss_content = @file_get_contents($rss_url);

// エラー処理
if ($rss_content === false) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'RSS取得に失敗しました']);
    exit;
}

// XMLをパース
$xml = simplexml_load_string($rss_content);

if (!$xml || !isset($xml->channel->item[0])) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'RSSの構造が不正です']);
    exit;
}

// 最新の記事を取得
$latest = $xml->channel->item[0];

// 必要な情報を抽出
$data = [
    'title'       => (string)$latest->title,
    'link'        => (string)$latest->link,
    'description' => (string)$latest->description,
    'pubDate'     => (string)$latest->pubDate,
];

// JSONとして出力
header('Content-Type: application/json; charset=utf-8');
echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
?>