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
$envFilePath = __DIR__ . '/.env'; // スクリプトと同じディレクトリにある場合

// .env ファイルを読み込む
loadEnv($envFilePath);

// 環境変数をechoする例
//echo "--- .env file content (via getenv() or \$_ENV) ---\n";
//echo "PEXELS_API_KEY: " . getenv('PEXELS_API_KEY') . "\n";

// APIキーを取得
$apiKey = getenv('PEXELS_API_KEY');

$num = 50;
$random_page = rand(1, 15);

// APIエンドポイント
$url = 'https://api.pexels.com/v1/curated?per_page='.$num.'&page='.$random_page;

// cURL 初期化
$ch = curl_init($url);

// cURL オプション設定
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Authorization: ' . $apiKey
]);

// API呼び出し
$response = curl_exec($ch);

// エラーチェック
if (curl_errno($ch)) {
    echo 'エラー: ' . curl_error($ch);
} else {
    // JSONデコードして結果を表示
    $data = json_decode($response, true);
    //print_r($data);
}

curl_close($ch);

$result = [];

foreach ($data['photos'] as $photo) {
    $result[] = [
        'photographer' => $photo['photographer'],
        'original'     => $photo['src']['original'],
    ];
}

// JSON形式で出力
header('Content-Type: application/json');

$random_number = rand(0, ($num-1));

// 配列が空だった場合。
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