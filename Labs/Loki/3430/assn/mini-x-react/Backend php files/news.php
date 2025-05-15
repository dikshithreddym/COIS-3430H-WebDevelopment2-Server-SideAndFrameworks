<?php
header('Content-Type: application/json');

$apiKey = 'b71bcb0c05994d35b53904c7c107aad5'; 
$url = "https://newsapi.org/v2/top-headlines?country=us&category=general&pageSize=10";

$ch = curl_init($url);
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_HTTPHEADER => [
        "User-Agent: MiniXReactApp/1.0",           
        "X-Api-Key: $apiKey"
    ]
]);

$response = curl_exec($ch);
curl_close($ch);

if (!$response) {
    echo json_encode(['success' => false, 'message' => 'News API fetch failed']);
    exit;
}

$data = json_decode($response, true);

if ($data['status'] !== 'ok') {
    echo json_encode([
        'success' => false,
        'message' => 'News API returned error',
        'debug' => $data
    ]);
    exit;
}

// Extract useful info
$articles = array_map(function($article) {
    return [
        'title' => $article['title'],
        'url' => $article['url'],
        'source' => $article['source']['name'],
        'publishedAt' => $article['publishedAt']
    ];
}, $data['articles']);

echo json_encode(['success' => true, 'articles' => $articles]);
