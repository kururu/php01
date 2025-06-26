<?php
$apiUrl = 'https://script.google.com/macros/s/AKfycbz5RWCwhOyBHzTkKIdgK4shELo6wiZIcdAzJK8iYehDzdMKsI3RNz3IO29729Td2LcH/exec';


// text01 を取得（GETまたはPOST）
$text = $_GET['text01'] ?? $_POST['text01'] ?? null;

/*
if (!$text) {
    exit('text01 が指定されていません');
}
*/
$text = $_GET['text01'] ?? '';  // null 対策

// --- GET送信 ---
$getUrl = $apiUrl . '?text01=' . urlencode($text);
$getResponse = file_get_contents($getUrl);

/*
// --- POST送信 ---
$postData = json_encode(['text01' => $text]);
$options = [
    'http' => [
        'header'  => "Content-Type: application/json\r\n",
        'method'  => 'POST',
        'content' => $postData,
    ],
];
$context = stream_context_create($options);
$postResponse = file_get_contents($apiUrl, false, $context);
*/
// 結果表示
echo "GETレスポンス: " . $getResponse . "<br>";
//echo "POSTレスポンス: " . $postResponse;
?>