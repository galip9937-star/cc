<?php
/**
 * Instagram User Lookup API
 * Günlük rate limit ile (IP başına 1 istek / 24 saat)
 * telegram : @unutur
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

$username = isset($_GET['user']) ? trim($_GET['user']) : '';

if (empty($username)) {
    echo json_encode([
        'success' => false,
        'error' => '❌ Kullanıcı adı gerekli',
        'ornek' => '/?user=elonmusk',
        'telegram' => '@unutur'
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    exit;
}

$username = ltrim($username, '@');

// Rate limit kontrolü (IP bazlı, 24 saat)
$ip = $_SERVER['REMOTE_ADDR'];
$rate_file = __DIR__ . '/ig_ratelimit_' . md5($ip) . '.json';

if (file_exists($rate_file)) {
    $data = json_decode(file_get_contents($rate_file), true);
    $last_request = $data['last_request'] ?? 0;
    $count = $data['count'] ?? 0;
    
    if ($count >= 1 && (time() - $last_request) < 86400) { // 24 saat
        $remaining = 86400 - (time() - $last_request);
        echo json_encode([
            'success' => false,
            'error' => '❌ Günlük limit aşıldı! 24 saatte 1 istek hakkınız var.',
            'remaining_seconds' => $remaining,
            'remaining_hours' => round($remaining / 3600, 1),
            'next_reset' => date('Y-m-d H:i:s', $last_request + 86400),
            'telegram' => '@unutur'
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        exit;
    }
}

// Instagram API isteği
$url = "https://www.instagram.com/api/graphql/";

$payload = [
    'lsd' => 'AdRs3OdVaQurU9jBNT0IjiKWV6s',
    'variables' => json_encode([
        'params' => [
            'event_request_id' => uniqid() . '-' . rand(1000, 9999),
            'search_query' => $username,
            'waterfall_id' => uniqid()
        ]
    ]),
    'doc_id' => '31115866268061587'
];

$headers = [
    'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
    'X-IG-App-ID: 936619743392459',
    'X-FB-LSD: AdRs3OdVaQurU9jBNT0IjiKWV6s',
    'Content-Type: application/x-www-form-urlencoded'
];

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($payload));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_TIMEOUT, 15);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($http_code != 200) {
    echo json_encode([
        'success' => false,
        'error' => '❌ Instagram API hatası',
        'http_code' => $http_code,
        'telegram' => '@unutur'
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    exit;
}

$data = json_decode($response, true);

// Rate limit kaydet
file_put_contents($rate_file, json_encode([
    'last_request' => time(),
    'count' => 1,
    'ip' => $ip
]));

// Sonuçları parse et
$contact_points = $data['data']['caa_ar_ig_account_search']['contact_points'] ?? [];

$results = [];
foreach ($contact_points as $cp) {
    $results[] = [
        'index' => $cp['index'],
        'contact_point' => $cp['contact_point'],
        'title' => $cp['title'],
        'type' => $cp['type']
    ];
}

echo json_encode([
    'success' => true,
    'username' => $username,
    'total' => count($results),
    'contact_points' => $results,
    'rate_limit' => [
        'limit' => '1 istek / 24 saat',
        'remaining' => 0,
        'resets_at' => date('Y-m-d H:i:s', time() + 86400)
    ],
    'telegram' => '@unutur'
], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
?>