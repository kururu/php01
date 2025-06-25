<?php

function loadEnv($filePath)
{
    if (!file_exists($filePath)) {
        echo "Error: .env file not found at " . $filePath . "\n";
        return;
    }

    $lines = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

    foreach ($lines as $line) {
        // コメント行をスキップ
        if (strpos(trim($line), '#') === 0) {
            continue;
        }

        // KEY=VALUE 形式の行を解析
        if (strpos($line, '=') !== false) {
            list($key, $value) = explode('=', $line, 2);
            $key = trim($key);
            $value = trim($value);

            // クォートを削除（例: "値" -> 値, '値' -> 値）
            if (preg_match('/^(\'|")(.*)(\1)$/', $value, $matches)) {
                $value = $matches[2];
            }

            // 環境変数として設定
            // putenv() は現在のプロセスでのみ有効
            putenv(sprintf('%s=%s', $key, $value));
            // $_ENV と $_SERVER も設定（オプション、$_ENV は php.ini の variables_order 設定による）
            $_ENV[$key] = $value;
            //$_SERVER[$key] = $value;
        }
    }
}

// .env ファイルのパスを指定
//$envFilePath = __DIR__ . '/.env'; // スクリプトと同じディレクトリにある場合

// .env ファイルを読み込む
//loadEnv($envFilePath);

// 環境変数をechoする例
//echo "--- .env file content (via getenv() or \$_ENV) ---\n";
//echo "PEXELS_API_KEY: " . getenv('PEXELS_API_KEY') . "\n";

// APIキー取得
$apiKey = getenv('PEXELS_API_KEY');

$num = 50;
$random_page = rand(1, 15);

// API URL
$url = 'https://api.pexels.com/v1/curated?per_page=' . $num . '&page=' . $random_page;

// HTTPヘッダー付きリクエストのためのコンテキスト作成
$options = [
    'http' => [
        'method' => 'GET',
        'header' => "Authorization: $apiKey\r\n"
    ]
];

$context = stream_context_create($options);

// API呼び出し
$response = @file_get_contents($url, false, $context);

if ($response === FALSE) {
    echo json_encode([
        'error' => 'API request failed'
    ]);
    exit;
}

// レスポンスをJSONとしてデコード
$data = json_decode($response, true);

$result = [];

foreach ($data['photos'] as $photo) {
    $result[] = [
        'photographer' => $photo['photographer'],
        'original'     => $photo['src']['original'],
    ];
}

header('Content-Type: application/json');

$random_number = rand(0, ($num - 1));

if (!isset($result[$random_number])) {
    $result[$random_number] = [
        "photographer" => "@Jessie Garcia",
        "original" => "https://images.pexels.com/photos/32539060/pexels-photo-32539060.jpeg"
    ];
}

$json = json_encode($result[$random_number], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
$json = str_replace('\/', '/', $json);

echo $json;


?>