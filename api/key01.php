<?php
// .envファイルを読み込み
$envFile = dirname(__DIR__) . '/.env';

if (file_exists($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) continue; // コメントをスキップ
        list($name, $value) = explode('=', $line, 2);
        $name = trim($name);
        $value = trim($value);

        // 値にクオートがある場合、取り除く
        $value = trim($value, "\"'");

        // 環境変数に設定（必要に応じて）
        putenv("$name=$value");

        if ($name === 'KEY') {
            echo "KEY: $value";
        }
    }
} else {
    echo ".env ファイルが見つかりませんでした。";
}
?>