<?php
header('Content-Type: application/json');

$key = getenv('KEY');

echo json_encode([
    'key' => $key ?: '@環境変数が設定されていません'
]);
?>