<?php

// Header içinde session başlatılıyor, o yüzden burada gerek yok
include 'header.php'; // HEADER BURADA ÇAĞRILIYOR

$recipe_id = $_GET['id'] ?? 0;
$user_id = $_SESSION['user_id'] ?? null;

// --- YORUM VE DETAY ÇEKME KODLARI (Aynı Kalıyor) ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_comment']) && $user_id) {
    $comment = trim($_POST['comment']); $rating = (int)$_POST['rating'];
    if (!empty($comment)) {
        $pdo->prepare("INSERT INTO comments (user_id, recipe_id, comment, rating) VALUES (?, ?, ?, ?)")->execute([$user_id, $recipe_id, $comment, $rating]);
        echo "<script>window.location.href='recipe.php?id=$recipe_id';</script>"; exit;
    }
}
$recipe = $pdo->query("SELECT r.*, u.username, u.avatar, c.name as category_name FROM recipes r JOIN users u ON r.user_id = u.id JOIN categories c ON r.category_id = c.id WHERE r.id = $recipe_id")->fetch(PDO::FETCH_ASSOC);
if (!$recipe) die("<div class='container my-5 alert alert-danger'>Tarif bulunamadı.</div>");
$pdo->query("UPDATE recipes SET views = views + 1 WHERE id = $recipe_id");

// Resimler
$images = $pdo->query("SELECT image_path FROM recipe_images WHERE recipe_id = $recipe_id")->fetchAll(PDO::FETCH_COLUMN);
if (empty($images) && !empty($recipe['image'])) $images[] = $recipe['image'];
if (empty($images)) $images[] = 'https://placehold.co/600x400/ffd1dc/white?text=Resim+Yok';

// Malzemeler ve Adımlar
$ingredients = $pdo->query("SELECT ingredient_text FROM recipe_ingredients WHERE recipe_id = $recipe_id")->fetchAll(PDO::FETCH_COLUMN);
$steps = $pdo->query("SELECT step_text FROM recipe_steps WHERE recipe_id = $recipe_id ORDER BY step_number")->fetchAll(PDO::FETCH_COLUMN);
$comments = $pdo->query("SELECT c.*, u.username, u.avatar FROM comments c JOIN users u ON c.user_id = u.id WHERE c.recipe_id = $recipe_id ORDER BY c.created_at DESC")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($recipe['title']) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body { background-color: #fff8f6; font-family: 'Poppins', sans-serif; color: #4a4a4a; }
        .white-card { background: white; border-radius: 15px; padding: 30px; box-shadow: 0 5px 20px rgba(0,0,0,0.05); }
        .main-img { width: 100%; height: 400px; object-fit: cover; border-radius: 10px; }
        .thumb-img { width: 100px; height: 80px; object-fit: cover; border-radius: 5px; cursor: pointer; opacity: 0.7; }
        .thumb-img:hover { opacity: 1; }
        
         /* Navbar */
        .navbar { background-color: #fff; box-shadow: 0 2px 10px rgba(0,0,0,0.05); padding: 0.8rem 0; }
        .navbar-brand { font-family: 'Pacifico', cursive; font-size: 2.2rem; color: var(--light-salmon) !important; text-shadow: 1px 1px 2px rgba(0,0,0,0.1); }
        .search-bar { border: 2px solid var(--peach-puff); border-radius: 20px; padding: 5px 15px; width: 300px; transition: all 0.3s; }
        .search-bar:focus { outline: none; border-color: var(--light-salmon); box-shadow: 0 0 5px var(--pastel-pink); }
        .nav-link { color: var(--text-dark); font-weight: 600; }
        .nav-link:hover { color: var(--light-salmon); }
    </style>
</head>
<body>
    <div class="container my-5">
        <div class="row">
            <div class="col-lg-8">
                <div class="white-card mb-4">
                    <h1 class="fw-bold"><?= htmlspecialchars($recipe['title']) ?></h1>
                    <p class="text-muted"><i class="fas fa-folder text-warning"></i> <?= $recipe['category_name'] ?> | <i class="fas fa-user text-warning"></i> <?= $recipe['username'] ?></p>
                    
                    <img src="<?= (strpos($images[0], 'http')===0 ? '' : 'uploads/recipes/') . $images[0] ?>" class="main-img mb-3" id="mainImage">
                    <?php if(count($images) > 1): ?>
                        <div class="d-flex gap-2">
                            <?php foreach($images as $img): ?>
                                <img src="uploads/recipes/<?= $img ?>" class="thumb-img" onclick="document.getElementById('mainImage').src=this.src">
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>

                    <h4 class="mt-4">Açıklama</h4><p><?= nl2br(htmlspecialchars($recipe['description'])) ?></p>
                    <h4 class="mt-4">Hazırlanış</h4>
                    <ol><?php foreach($steps as $s) echo "<li>".htmlspecialchars($s)."</li>"; ?></ol>
                </div>
                
               

            <div class="col-lg-4">
                <div class="white-card bg-light">
                    <h4>Malzemeler</h4>
                    <ul><?php foreach($ingredients as $i) echo "<li>".htmlspecialchars($i)."</li>"; ?></ul>
                </div>
            </div>

             <div class="white-card">
                    <h4>Yorumlar (<?= count($comments) ?>)</h4>
                    <?php if($user_id): ?>
                        <form method="POST" class="mb-4">
                            <select name="rating" class="form-select w-auto mb-2"><option value="5">⭐⭐⭐⭐⭐</option><option value="4">⭐⭐⭐⭐</option></select>
                            <textarea name="comment" class="form-control mb-2" required placeholder="Yorum yaz..."></textarea>
                            <button type="submit" name="submit_comment" class="btn text-white" style="background-color: #ffa07a;">Gönder</button>
                        </form>
                    <?php endif; ?>
                    <?php foreach($comments as $c): ?>
                        <div class="border-bottom pb-2 mb-2">
                            <strong><?= htmlspecialchars($c['username']) ?></strong> <span class="text-warning"><?= str_repeat('★', $c['rating']) ?></span>
                            <p class="mb-0"><?= htmlspecialchars($c['comment']) ?></p>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>