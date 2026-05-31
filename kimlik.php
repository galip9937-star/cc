<?php
// kimlik.php - Direkt fotoğraf döndüren versiyon
$min = 1;
$max = 98;

// 1. DURUM: Eğer ?id=19 gibi bir sayı geldiyse, O FOTOĞRAFI GÖSTER
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = (int)$_GET['id'];

    // ID'nin 1 ile 98 arasında olduğunu kontrol et
    if ($id >= $min && $id <= $max) {
        // Dosya adını oluştur (örnek: 19.jpg)
        $dosyaAdi = $id . ".jpg";
        
        // Dosya varsa göster, yoksa hata ver
        if (file_exists($dosyaAdi)) {
            header('Content-Type: image/jpeg');
            readfile($dosyaAdi);
            exit;
        } else {
            // Dosya yoksa 404 hatası
            http_response_code(404);
            echo "Dosya bulunamadı: " . $dosyaAdi;
            exit;
        }
    } else {
        // ID 1-98 aralığında değilse hata
        http_response_code(400);
        echo "ID $min ile $max arasında olmalıdır.";
        exit;
    }
}

// 2. DURUM: Eğer ?id=... yoksa (örnek: /kimlik.php), RASTGELE BİR FOTOĞRAF GÖSTER
$rastgeleId = mt_rand($min, $max);
$rastgeleDosya = $rastgeleId . ".jpg";

// Rastgele dosyayı göster
if (file_exists($rastgeleDosya)) {
    header('Content-Type: image/jpeg');
    readfile($rastgeleDosya);
    exit;
} else {
    // Eğer rastgele gelen dosya bile yoksa hata ver (bu çok düşük ihtimal)
    http_response_code(500);
    echo "Sistem hatası: Rastgele dosya bulunamadı.";
}
?>