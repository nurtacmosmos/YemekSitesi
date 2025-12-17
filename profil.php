<?php
// profil.php - STİL VE MENÜ DAHİL (TEK DOSYA ÇÖZÜMÜ)
require_once 'db.php';

// Oturum başlatılmamışsa başlat
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// -----------------------------------------------------------------------
// 1. KATEGORİLERİ ÇEK (Menü İçin)
// -----------------------------------------------------------------------
$header_categories = [];
try {
    $catQuery = $pdo->query("SELECT * FROM categories ORDER BY name ASC");
    if($catQuery) $header_categories = $catQuery->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {}


// -----------------------------------------------------------------------
// 2. PROFİL İŞLEMLERİ (Kullanıcı Belirleme)
// -----------------------------------------------------------------------
$session_user_id = $_SESSION['user_id'] ?? null; // Giriş yapan kişi
$target_user_id = null; // Görüntülenecek kişi

// Kimi görüntülüyoruz? (URL'de id varsa o, yoksa oturum açan kişi)
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $target_user_id = (int)$_GET['id'];
} elseif ($session_user_id) {
    $target_user_id = $session_user_id;
} else {
    // İkisi de yoksa anasayfaya
    header('Location: index.php');
    exit;
}

// Yetki ve Profil Kontrolü
$is_own_profile = ($session_user_id && $session_user_id === $target_user_id);
$is_admin = isset($_SESSION['role']) && $_SESSION['role'] === 'admin';

// Hedef Kullanıcı Bilgilerini Çek
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$target_user_id]);
$profileUser = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$profileUser) {
    die("<div class='container my-5 alert alert-danger'>Kullanıcı bulunamadı.</div>");
}

// Hedef Kullanıcının Tariflerini Çek
$stmt = $pdo->prepare("
    SELECT r.*, c.name as category_name 
    FROM recipes r 
    JOIN categories c ON r.category_id = c.id 
    WHERE r.user_id = ? 
    ORDER BY r.created_at DESC
");
$stmt->execute([$target_user_id]);
$user_recipes = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Mesaj Yakalama
$message = $_GET['msg'] ?? '';
$error = $_GET['err'] ?? '';

// Tarif Silme İşlemi (Sadece kendi profili ise)
if ($is_own_profile && isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['recipe_id'])) {
    $r_id = $_GET['recipe_id'];
    $pdo->prepare("DELETE FROM recipes WHERE id = ? AND user_id = ?")->execute([$r_id, $session_user_id]);
    header("Location: profil.php?msg=Tarif başarıyla silindi.");
    exit;
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($profileUser['username']) ?> Profili | Lezzet Bahçesi</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <link href="https://fonts.googleapis.com/css2?family=Pacifico&family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    
    <style>
        /* --- RENK PALETİ VE GENEL STİL (INDEX İLE AYNI) --- */
        :root { 
            --powder-blue: #b0e0e6; 
            --pastel-pink: #ffd1dc; 
            --light-salmon: #ffa07a; 
            --peach-puff: #ffdab9; 
            --snow-white: #fff8f6; 
            --text-dark: #4a4a4a; 
        }
        body { background-color: var(--snow-white); font-family: 'Poppins', sans-serif; color: var(--text-dark); }
        
        /* --- NAVBAR STİLLERİ (Index ile Aynı) --- */
        .navbar { background-color: #fff; box-shadow: 0 2px 10px rgba(0,0,0,0.05); padding: 0.8rem 0; }
        .navbar-brand { font-family: 'Pacifico', cursive; font-size: 2.2rem; color: var(--light-salmon) !important; text-shadow: 1px 1px 2px rgba(0,0,0,0.1); }
        .search-bar { border: 2px solid var(--peach-puff); border-radius: 20px; padding: 5px 15px; width: 300px; transition: all 0.3s; }
        .search-bar:focus { outline: none; border-color: var(--light-salmon); box-shadow: 0 0 5px var(--pastel-pink); }
        .nav-link { color: var(--text-dark); font-weight: 500; }
        .nav-link:hover { color: var(--light-salmon); }

        /* --- PROFİL STİLLERİ --- */
        .nav-tabs .nav-link { color: var(--text-dark); border: none; font-weight: 600; }
        .nav-tabs .nav-link.active { color: var(--light-salmon); border-bottom: 3px solid var(--light-salmon); background: none; }
        .badge-role { background-color: var(--powder-blue); color: #444; font-size: 0.9rem; padding: 5px 10px; border-radius: 20px; }
        .badge-admin { background-color: var(--light-salmon); color: white; }
    </style>
</head>
<body>
    
    <?php include 'header.php'; ?>

    <div class="container my-5">
        <div class="row">
            <div class="col-md-4 mb-4">
                <div class="card border-0 shadow-sm p-4 text-center">
                    <div class="position-relative d-inline-block mx-auto">
                        <?php 
                            $pAvatar = !empty($profileUser['avatar']) && file_exists('uploads/avatars/'.$profileUser['avatar']) 
                                ? 'uploads/avatars/'.$profileUser['avatar'] 
                                : 'https://placehold.co/150x150/ffa07a/white?text=' . strtoupper(substr($profileUser['username'],0,1));
                        ?>
                        <img src="<?= $pAvatar ?>" class="rounded-circle mb-3 border" width="120" height="120" style="object-fit: cover;">
                        
                        <?php if($profileUser['role'] === 'admin'): ?>
                            <span class="position-absolute bottom-0 end-0 badge badge-admin border border-white">Admin</span>
                        <?php elseif($profileUser['role'] === 'manager'): ?>
                            <span class="position-absolute bottom-0 end-0 badge border border-white" style="background-color: #ffc107; color: #333;">Yönetici</span>
                        <?php else: ?>
                            <span class="position-absolute bottom-0 end-0 badge badge-role border border-white">Üye</span>
                        <?php endif; ?>
                    </div>
                    
                    <h4><?= htmlspecialchars($profileUser['username']) ?></h4>
                    
                    <?php if($is_own_profile): ?>
                        <p class="text-muted small"><?= htmlspecialchars($profileUser['email']) ?></p>
                        <a href="recipe_form.php" class="btn w-100 mb-2 text-white" style="background-color: var(--light-salmon);">
                            <i class="fas fa-plus-circle me-2"></i> Yeni Tarif Ekle
                        </a>
                    <?php else: ?>
                        <p class="text-muted small">Tarif Yazarı</p>
                        <a href="mailto:<?= htmlspecialchars($profileUser['email']) ?>" class="btn btn-outline-secondary w-100 btn-sm">
                            <i class="fas fa-envelope me-2"></i> İletişime Geç
                        </a>
                    <?php endif; ?>
                </div>
            </div>

            <div class="col-md-8">
                <?php if($message): ?><div class="alert alert-success"><?= htmlspecialchars($message) ?></div><?php endif; ?>
                <?php if($error): ?><div class="alert alert-danger"><?= htmlspecialchars($error) ?></div><?php endif; ?>

                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white">
                        <ul class="nav nav-tabs card-header-tabs" id="profileTabs" role="tablist">
                            <li class="nav-item">
                                <button class="nav-link active" id="recipes-tab" data-bs-toggle="tab" data-bs-target="#recipes" type="button">
                                    <?= $is_own_profile ? 'Tariflerim' : 'Yazarın Tarifleri' ?>
                                </button>
                            </li>
                            
                            <?php if($is_own_profile): ?>
                                <li class="nav-item">
                                    <button class="nav-link" id="settings-tab" data-bs-toggle="tab" data-bs-target="#settings" type="button">Profil Düzenle</button>
                                </li>
                            <?php endif; ?>
                        </ul>
                    </div>
                    
                    <div class="card-body">
                        <div class="tab-content" id="profileTabsContent">
                            
                            <div class="tab-pane fade show active" id="recipes" role="tabpanel">
                                <?php if (count($user_recipes) > 0): ?>
                                    <div class="list-group list-group-flush">
                                        <?php foreach($user_recipes as $recipe): ?>
                                        <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                                            <div class="d-flex align-items-center">
                                                <?php 
                                                    $recImg = !empty($recipe['image']) && file_exists('uploads/recipes/'.$recipe['image']) 
                                                        ? 'uploads/recipes/'.$recipe['image'] 
                                                        : 'https://placehold.co/60x60/b0e0e6/white?text=Tarif';
                                                ?>
                                                <img src="<?= $recImg ?>" width="60" height="60" class="rounded me-3" style="object-fit:cover;">
                                                
                                                <div>
                                                    <a href="recipe.php?id=<?= $recipe['id'] ?>" class="text-decoration-none text-dark">
                                                        <h6 class="mb-0 fw-bold"><?= htmlspecialchars($recipe['title']) ?></h6>
                                                    </a>
                                                    <small class="text-muted">
                                                        <?= $recipe['views'] ?> Görüntülenme • <?= date('d.m.Y', strtotime($recipe['created_at'])) ?>
                                                        • <span class="badge bg-light text-dark border"><?= htmlspecialchars($recipe['category_name']) ?></span>
                                                    </small>
                                                </div>
                                            </div>

                                            <div>
                                                <?php if($is_own_profile): ?>
                                                    <a href="recipe_form.php?id=<?= $recipe['id'] ?>" class="btn btn-sm btn-light text-primary" title="Düzenle"><i class="fas fa-edit"></i></a>
                                                    <a href="profil.php?action=delete&recipe_id=<?= $recipe['id'] ?>" onclick="return confirm('Silmek istediğine emin misin?')" class="btn btn-sm btn-light text-danger" title="Sil"><i class="fas fa-trash"></i></a>
                                                <?php else: ?>
                                                    <a href="recipe.php?id=<?= $recipe['id'] ?>" class="btn btn-sm btn-outline-secondary">İncele</a>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                        <?php endforeach; ?>
                                    </div>
                                <?php else: ?>
                                    <p class="text-muted text-center py-4">Henüz tarif eklenmemiş.</p>
                                <?php endif; ?>
                            </div>

                            <?php if($is_own_profile): ?>
                            <div class="tab-pane fade" id="settings" role="tabpanel">
                                <form action="profile_action.php" method="POST" enctype="multipart/form-data">
                                    <div class="mb-3">
                                        <label class="form-label text-muted small">Profil Fotoğrafı</label>
                                        <input type="file" name="avatar" class="form-control form-control-sm">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label text-muted small">Kullanıcı Adı</label>
                                        <input type="text" name="username" class="form-control" value="<?= htmlspecialchars($profileUser['username']) ?>" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label text-muted small">E-posta Adresi</label>
                                        <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($profileUser['email']) ?>" required>
                                    </div>
                                    <hr class="my-4">
                                    <div class="mb-3">
                                        <label class="form-label text-muted small">Yeni Şifre (İsteğe bağlı)</label>
                                        <input type="password" name="new_password" class="form-control" placeholder="******">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label text-muted small">Onay İçin Mevcut Şifre</label>
                                        <input type="password" name="current_password" class="form-control" required>
                                    </div>
                                    <button type="submit" class="btn text-white w-100" style="background-color: var(--light-salmon);">Güncelle</button>
                                </form>
                            </div>
                            <?php endif; ?>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>