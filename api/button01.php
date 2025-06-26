<?php
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $filename = __DIR__ . "/memo01.txt";
    $date = date("Y-m-d H:i:s");

    // ファイルに書き込み（追記）
    file_put_contents($filename, $date . PHP_EOL, FILE_APPEND);
    echo "<p>メモを保存しました: $date</p>";
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>メモ記録</title>
</head>
<body>
    <form method="post">
        <button type="submit">日付をメモする</button>
    </form>
</body>
</html>