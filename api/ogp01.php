<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

$url = filter_input(INPUT_GET, 'url', FILTER_VALIDATE_URL);

if (!$url) {
    echo json_encode(['error' => 'Invalid URL']);
    exit;
}

$html = @file_get_contents($url);

if (!$html) {
    echo json_encode(['error' => 'Could not fetch URL']);
    exit;
}

libxml_use_internal_errors(true);
$doc = new DOMDocument();
$doc->loadHTML($html);
$xpath = new DOMXPath($doc);
$metaTags = $xpath->query("//meta[@property='og:image']");

$ogImage = null;
if ($metaTags->length > 0) {
    $ogImage = $metaTags[0]->getAttribute('content');
}

echo json_encode(['og_image' => $ogImage]);

?>