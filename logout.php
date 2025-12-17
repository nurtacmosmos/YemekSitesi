<?php
// logout.php
session_start(); // Oturumu başlat (mevcut oturuma erişmek için)

// Tüm oturum değişkenlerini temizle
$_SESSION = [];

// Oturumu tamamen yok et
session_destroy();

// Kullanıcıyı anasayfaya (veya giriş sayfasına) yönlendir
header('Location: index.php');
exit;
?>