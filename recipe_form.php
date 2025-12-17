<?php
// recipe_form.php - DÜZELTİLMİŞ RESİM MANTIĞI
require_once 'db.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$is_edit = false;
$recipe = ['title' => '', 'description' => '', 'category_id' => '', 'image' => ''];
$ingredients = [];
$steps = [];
$images = [];

$upload_dir = 'uploads/recipes/';
if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);

// Kategorileri çek
$categories = $pdo->query("SELECT id, name FROM categories ORDER BY name ASC")->fetchAll(PDO::FETCH_ASSOC);

// DÜZENLEME MODU
if (isset($_GET['id'])) {
    $is_edit = true;
    $recipe_id = $_GET['id'];

    $recipe_stmt = $pdo->prepare("SELECT * FROM recipes WHERE id = ? AND user_id = ?");
    $recipe_stmt->execute([$recipe_id, $user_id]);
    $recipe = $recipe_stmt->fetch(PDO::FETCH_ASSOC);

    if (!$recipe) die("Tarif bulunamadı veya yetkiniz yok.");
    
    $ingredients = $pdo->prepare("SELECT * FROM recipe_ingredients WHERE recipe_id = ?")->execute([$recipe_id]) ? $pdo->prepare("SELECT * FROM recipe_ingredients WHERE recipe_id = ?")->execute([$recipe_id]) : []; // Hata önleme
    $ingredients_stmt = $pdo->prepare("SELECT ingredient_text FROM recipe_ingredients WHERE recipe_id = ?");
    $ingredients_stmt->execute([$recipe_id]);
    $ingredients = $ingredients_stmt->fetchAll(PDO::FETCH_COLUMN);

    $steps_stmt = $pdo->prepare("SELECT step_text FROM recipe_steps WHERE recipe_id = ? ORDER BY step_number");
    $steps_stmt->execute([$recipe_id]);
    $steps = $steps_stmt->fetchAll(PDO::FETCH_COLUMN);

    $images_stmt = $pdo->prepare("SELECT image_path FROM recipe_images WHERE recipe_id = ?");
    $images_stmt->execute([$recipe_id]);
    $images = $images_stmt->fetchAll(PDO::FETCH_COLUMN);
}

// FORM GÖNDERİMİ
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $category_id = $_POST['category_id'];
    $ingredients_list = $_POST['ingredient'] ?? [];
    $steps_list = $_POST['step'] ?? [];
    
    $pdo->beginTransaction();

    try {
        if ($is_edit) {
            $update_stmt = $pdo->prepare("UPDATE recipes SET title=?, description=?, category_id=? WHERE id=? AND user_id=?");
            $update_stmt->execute([$title, $description, $category_id, $recipe_id, $user_id]);
            
            $pdo->prepare("DELETE FROM recipe_ingredients WHERE recipe_id = ?")->execute([$recipe_id]);
            $pdo->prepare("DELETE FROM recipe_steps WHERE recipe_id = ?")->execute([$recipe_id]);
        } else {
            $insert_stmt = $pdo->prepare("INSERT INTO recipes (user_id, title, description, category_id) VALUES (?, ?, ?, ?)");
            $insert_stmt->execute([$user_id, $title, $description, $category_id]);
            $recipe_id = $pdo->lastInsertId();
        }

        // Malzemeler
        $ing_stmt = $pdo->prepare("INSERT INTO recipe_ingredients (recipe_id, ingredient_text) VALUES (?, ?)");
        foreach ($ingredients_list as $ing) if (!empty(trim($ing))) $ing_stmt->execute([$recipe_id, trim($ing)]);

        // Adımlar
        $step_stmt = $pdo->prepare("INSERT INTO recipe_steps (recipe_id, step_number, step_text) VALUES (?, ?, ?)");
        foreach ($steps_list as $index => $step) if (!empty(trim($step))) $step_stmt->execute([$recipe_id, $index + 1, trim($step)]);

        // --- RESİM YÜKLEME (DÜZELTİLEN KISIM) ---
        if (isset($_FILES['images'])) {
            $current_img_count = $is_edit ? count($images) : 0;
            $uploaded_files = reArrayFiles($_FILES['images']);

            foreach ($uploaded_files as $file) {
                if ($current_img_count >= 5) break;

                if ($file['error'] === UPLOAD_ERR_OK) {
                    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
                    $new_name = uniqid() . '_' . time() . '.' . $ext;
                    $target_file = $upload_dir . $new_name;

                    if (move_uploaded_file($file['tmp_name'], $target_file)) {
                        // 1. Galeri tablosuna ekle
                        $pdo->prepare("INSERT INTO recipe_images (recipe_id, image_path) VALUES (?, ?)")->execute([$recipe_id, $new_name]);
                        
                        // 2. Ana Tabloyu Güncelle (Eğer ana resim boşsa veya yeni yükleme yapılıyorsa)
                        // Bu satır sayesinde anasayfada resim görünecek
                        $update_main = $pdo->prepare("UPDATE recipes SET image = ? WHERE id = ? AND (image IS NULL OR image = '')");
                        $update_main->execute([$new_name, $recipe_id]);

                        $current_img_count++;
                    }
                }
            }
        }
        
        $pdo->commit();
        header('Location: profil.php?status=' . ($is_edit ? 'updated' : 'created'));
        exit;

    } catch (Exception $e) {
        $pdo->rollBack();
        $error = "Hata: " . $e->getMessage();
    }
}

function reArrayFiles(&$file_post) {
    $file_ary = array();
    $file_count = count($file_post['name']);
    $file_keys = array_keys($file_post);
    for ($i=0; $i<$file_count; $i++) foreach ($file_keys as $key) $file_ary[$i][$key] = $file_post[$key][$i];
    return $file_ary;
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title><?php echo $is_edit ? 'Tarif Düzenle' : 'Yeni Tarif'; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root { --light-salmon: #ffa07a; }
        body { background-color: #fff8f6; }
        .btn-theme { background-color: var(--light-salmon); color: white; }
    </style>
</head>
<body>
    <div class="container my-5" style="max-width:800px;">
        <h2 class="mb-4"><?php echo $is_edit ? 'Tarifi Düzenle' : 'Yeni Tarif Ekle'; ?></h2>
        
        <?php if (isset($error)): ?><div class="alert alert-danger"><?= $error ?></div><?php endif; ?>

        <form method="POST" enctype="multipart/form-data">
            
            <div class="card p-4 mb-3">
                <label class="form-label">Tarif Adı</label>
                <input type="text" name="title" class="form-control" value="<?= htmlspecialchars($recipe['title']) ?>" required>
                
                <label class="form-label mt-3">Açıklama</label>
                <textarea name="description" class="form-control" rows="3" required><?= htmlspecialchars($recipe['description']) ?></textarea>

                <label class="form-label mt-3">Kategori</label>
                <select name="category_id" class="form-select" required>
                    <option value="">Seçiniz</option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?= $cat['id'] ?>" <?= ($recipe['category_id'] == $cat['id']) ? 'selected' : '' ?>><?= htmlspecialchars($cat['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="card p-4 mb-3">
                <h5>Malzemeler</h5>
                <div id="ing-container">
                    <?php if($ingredients): foreach($ingredients as $ing): ?>
                        <div class="input-group mb-2"><input type="text" name="ingredient[]" class="form-control" value="<?= htmlspecialchars($ing) ?>"><button type="button" class="btn btn-danger remove-btn">X</button></div>
                    <?php endforeach; else: ?>
                        <div class="input-group mb-2"><input type="text" name="ingredient[]" class="form-control" placeholder="Örn: 1 bardak süt"><button type="button" class="btn btn-danger remove-btn">X</button></div>
                    <?php endif; ?>
                </div>
                <button type="button" class="btn btn-sm btn-outline-secondary" onclick="addItem('ing-container', 'ingredient')">+ Malzeme Ekle</button>
            </div>

            <div class="card p-4 mb-3">
                <h5>Hazırlanış</h5>
                <div id="step-container">
                    <?php if($steps): foreach($steps as $step): ?>
                        <div class="input-group mb-2"><textarea name="step[]" class="form-control" rows="1"><?= htmlspecialchars($step) ?></textarea><button type="button" class="btn btn-danger remove-btn">X</button></div>
                    <?php endforeach; else: ?>
                        <div class="input-group mb-2"><textarea name="step[]" class="form-control" rows="1" placeholder="Adım 1..."></textarea><button type="button" class="btn btn-danger remove-btn">X</button></div>
                    <?php endif; ?>
                </div>
                <button type="button" class="btn btn-sm btn-outline-secondary" onclick="addItem('step-container', 'step')">+ Adım Ekle</button>
            </div>

            <div class="card p-4 mb-3">
                <h5>Resimler</h5>
                <?php if($images): ?>
                    <div class="d-flex gap-2 mb-2">
                        <?php foreach($images as $img): ?>
                            <img src="<?= $upload_dir . $img ?>" style="width:80px; height:80px; object-fit:cover; border-radius:5px;">
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
                <input type="file" name="images[]" class="form-control" multiple accept="image/*">
            </div>

            <button type="submit" class="btn btn-theme w-100 btn-lg">Kaydet</button>
        </form>
    </div>

    <script>
        function addItem(containerId, name) {
            const div = document.createElement('div');
            div.className = 'input-group mb-2';
            div.innerHTML = `<input type="${name == 'step' ? 'hidden' : 'text'}" class="form-control" name="${name}[]" ${name == 'step' ? '' : 'placeholder="..."'}> ${name == 'step' ? '<textarea name="step[]" class="form-control" rows="1"></textarea>' : ''} <button type="button" class="btn btn-danger remove-btn">X</button>`;
            document.getElementById(containerId).appendChild(div);
            div.querySelector('.remove-btn').onclick = function() { this.parentElement.remove(); }
        }
        document.querySelectorAll('.remove-btn').forEach(btn => btn.onclick = function() { this.parentElement.remove(); });
    </script>
</body>
</html>