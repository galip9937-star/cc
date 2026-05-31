<?php
// kimlik.php
$min = 1;
$max = 98;

$telegram = "@unutur";
$yapan = "⛧ 𝐋𝐨𝐫𝐞𝐱 ⛧";

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = (int)$_GET['id'];
    if ($id >= $min && $id <= $max) {
        $dosya = str_pad($id, 2, "0", STR_PAD_LEFT) . ".jpg";
        $yol = $dosya;
        
        if (file_exists($yol)) {
            header('Content-Type: image/jpeg');
            readfile($yol);
            exit;
        } else {
            http_response_code(404);
            echo "Dosya bulunamadı: " . $dosya;
            exit;
        }
    } else {
        http_response_code(400);
        echo "ID $min-$max arasında olmalı";
        exit;
    }
}

$random = mt_rand($min, $max);
$dosya = str_pad($random, 2, "0", STR_PAD_LEFT) . ".jpg";

$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https://' : 'http://';
$host = $_SERVER['HTTP_HOST'];
$baseUrl = $protocol . $host . rtrim(dirname($_SERVER['SCRIPT_NAME']), '/');
$imageUrl = $baseUrl . "/kimlik.php?id=" . $random;

// JSON'u düzgün formatla (UTF-8, eğik çizgileri kaçırma)
header('Content-Type: application/json; charset=utf-8');
echo json_encode([
    "id" => $random,
    "file" => $dosya,
    "url" => $imageUrl,
    "developer" => $yapan,
    "telegram" => $telegram
], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
?>