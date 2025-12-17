<?php
// fix_images.php - Veritabanındaki resim eşleşmelerini tamir eder
require_once 'db.php';

echo "<h3>Resim Onarım İşlemi Başladı...</h3>";

// 1. Resmi olmayan tarifleri bul
$stmt = $pdo->query("SELECT id, title FROM recipes WHERE image IS NULL OR image = ''");
$recipes = $stmt->fetchAll(PDO::FETCH_ASSOC);

$count = 0;
foreach ($recipes as $recipe) {
    // 2. Bu tarife ait recipe_images tablosunda resim var mı bak
    $imgStmt = $pdo->prepare("SELECT image_path FROM recipe_images WHERE recipe_id = ? LIMIT 1");
    $imgStmt->execute([$recipe['id']]);
    $image = $imgStmt->fetch(PDO::FETCH_ASSOC);

    if ($image) {
        // 3. Varsa, ana tablodaki 'image' sütununa bu ismi yaz
        $updateStmt = $pdo->prepare("UPDATE recipes SET image = ? WHERE id = ?");
        $updateStmt->execute([$image['image_path'], $recipe['id']]);
        
        echo "✅ <b>{$recipe['title']}</b>: Ana resim güncellendi -> {$image['image_path']}<br>";
        $count++;
    } else {
        echo "❌ <b>{$recipe['title']}</b>: Bu tarife ait hiç resim bulunamadı.<br>";
    }
}

echo "<hr><h3>İşlem Tamamlandı. Toplam $count tarif onarıldı.</h3>";
echo "<a href='index.php'>Anasayfaya Dön</a>";
?>