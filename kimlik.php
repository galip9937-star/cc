<?php
// kimlik.php
$min = 1;
$max = 98;

// Telegram bilgisi
$telegram = "@unutur";
$yapan = " ⛧ 𝐋𝐨𝐫𝐞𝐱 ⛧ ";

// ID var mı kontrol et
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = (int)$_GET['id'];
    if ($id >= $min && $id <= $max) {
        $dosya = str_pad($id, 2, "0", STR_PAD_LEFT) . ".jpg";
        // Ana depoda olduğu için klasör yok, direkt dosya
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

// ID yoksa random JSON döndür
$random = mt_rand($min, $max);
$dosya = str_pad($random, 2, "0", STR_PAD_LEFT) . ".jpg";

$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https://' : 'http://';
$baseUrl = $protocol . $_SERVER['HTTP_HOST'] . rtrim(dirname($_SERVER['SCRIPT_NAME']), '/');
$imageUrl = $baseUrl . "/kimlik.php?id=" . $random;

header('Content-Type: application/json');
echo json_encode([
    "id" => $random,
    "file" => $dosya,
    "url" => $imageUrl,
    "developer" => $yapan,
    "telegram" => $telegram
], JSON_PRETTY_PRINT);
?>