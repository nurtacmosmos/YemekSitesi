<?php
$host = 'localhost';
$dbname = 'lezzet_bahcesi';
$username = 'root'; // Kendi kullanıcı adın
$password = ''; // Kendi şifren

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Veritabanı hatası: " . $e->getMessage());
}
?>