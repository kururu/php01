<?php
$origin = $_SERVER['HTTP_ORIGIN'] ?? '';
if ($origin === 'null' || $origin === 'https://kururu.github.io') {
    header("Access-Control-Allow-Origin: $origin");
    header("Content-Type: application/json");
}

// .envファイルから環境変数を読み込む簡易関数（dotenvライブラリなし）
function loadEnv($filePath) {
    if (!file_exists($filePath)) return;
    $lines = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) continue;
        if (strpos($line, '=') !== false) {
            list($key, $value) = explode('=', $line, 2);
            $key = trim($key);
            $value = trim($value, "\"'"); // クォート削除
            putenv("$key=$value");
        }
    }
}

// .envを読み込み
//loadEnv(__DIR__ . '/.env');

$accessToken = getenv('ACCESS_TOKEN');

//echo $accessToken;

$apiBaseUrl = 'https://graph.threads.net/v1.0';

function get_user_id($accessToken, $apiBaseUrl) {
    $url = $apiBaseUrl . '/me';

    // HTTPヘッダーを指定
    $options = [
        'http' => [
            'method'  => 'GET',
            'header'  => "Authorization: Bearer $accessToken\r\n"
        ]
    ];

    $context = stream_context_create($options);
    $response = @file_get_contents($url, false, $context); // @で警告非表示

    // HTTPステータスコードを取得（$http_response_header を利用）
    $httpCode = 0;
    if (isset($http_response_header)) {
        foreach ($http_response_header as $header) {
            if (preg_match('#^HTTP/\d+\.\d+\s+(\d+)#', $header, $matches)) {
                $httpCode = (int)$matches[1];
                break;
            }
        }
    }

    if ($httpCode === 200 && $response) {
        $data = json_decode($response, true);
        return $data['id'] ?? null;
    } else {
        echo "ユーザーIDの取得に失敗: " . $response;
        return null;
    }
}

// 実行例
$userId = get_user_id($accessToken, $apiBaseUrl);
echo "User ID: " . $userId;

function get_latest_thread($accessToken, $apiBaseUrl) {
    $url = $apiBaseUrl . '/me/threads';
    $params = http_build_query([
        'fields' => 'id,media_product_type,media_type,media_url,permalink,owner,username,text,timestamp,shortcode,thumbnail_url,children,is_quote_post,link_attachment_url',
        'limit' => 10
    ]);
    $urlWithParams = $url . '?' . $params;

    $options = [
        'http' => [
            'method' => 'GET',
            'header' => "Authorization: Bearer $accessToken\r\n"
        ]
    ];

    $context = stream_context_create($options);
    $response = @file_get_contents($urlWithParams, false, $context);

    // HTTP ステータスコードの取得
    $httpCode = 0;
    if (isset($http_response_header)) {
        foreach ($http_response_header as $header) {
            if (preg_match('#^HTTP/\d+\.\d+\s+(\d+)#', $header, $matches)) {
                $httpCode = (int)$matches[1];
                break;
            }
        }
    }

    if ($httpCode === 200 && $response) {
        $data = json_decode($response, true);

        header('Content-Type: application/json; charset=utf-8');

        $json = json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        $json = str_replace('\/', '/', $json); // \/ を / に置換
        echo $json;
        return;
    } else {
        header('Content-Type: text/plain; charset=utf-8');
        echo "投稿の取得に失敗: " . ($response ?: 'No response');
        return null;
    }
}

// 実行例
get_latest_thread($accessToken, $apiBaseUrl);

?>