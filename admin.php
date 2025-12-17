<?php
// admin.php - BÖLÜNMÜŞ YÖNETİM PANELİ
require_once 'db.php';
session_start();

// --- 1. GÜVENLİK KONTROLÜ ---
if (!isset($_SESSION['user_id'])) { header('Location: index.php'); exit; }

$stmt = $pdo->prepare("SELECT role FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$currentUserRole = $stmt->fetchColumn();

// Sadece Admin ve Manager girebilir
if ($currentUserRole !== 'admin' && $currentUserRole !== 'manager') {
    die("Yetkisiz Erişim. <a href='index.php'>Anasayfa</a>");
}

// --- 2. SAYFA BELİRLEME ---
// URL'den hangi sayfada olduğumuzu alıyoruz (Varsayılan: users)
$page = $_GET['page'] ?? 'users';

// --- 3. İŞLEMLER (POST/GET) ---

// Rol Güncelleme (SADECE MANAGER)
if (isset($_POST['update_role']) && $currentUserRole === 'manager') {
    $uid = $_POST['user_id'];
    $new_role = $_POST['role'];
    if ($uid != $_SESSION['user_id']) {
        $pdo->prepare("UPDATE users SET role = ? WHERE id = ?")->execute([$new_role, $uid]);
        header("Location: admin.php?page=users&msg=Rol güncellendi"); exit;
    }
}

// Silme İşlemleri
if (isset($_GET['delete_type']) && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $type = $_GET['delete_type'];

    if ($type == 'recipe') {
        $pdo->prepare("DELETE FROM recipes WHERE id = ?")->execute([$id]);
        header("Location: admin.php?page=recipes&msg=Tarif silindi"); exit;
    } elseif ($type == 'user' && $id != $_SESSION['user_id']) {
        $pdo->prepare("DELETE FROM users WHERE id = ?")->execute([$id]);
        header("Location: admin.php?page=users&msg=Kullanıcı silindi"); exit;
    } elseif ($type == 'comment') {
        $pdo->prepare("DELETE FROM comments WHERE id = ?")->execute([$id]);
        header("Location: admin.php?page=comments&msg=Yorum silindi"); exit;
    }
}

// --- 4. VERİ ÇEKME (Sadece aktif sayfa için veri çekiyoruz) ---

// A) KULLANICILAR SAYFASI VERİLERİ
if ($page == 'users') {
    $user_search = $_GET['q'] ?? '';
    $user_sql = "SELECT * FROM users WHERE username LIKE ? ORDER BY role DESC, created_at DESC";
    $users = $pdo->prepare($user_sql);
    $users->execute(["%$user_search%"]);
    $total_users = $users->rowCount();
}

// B) TARİFLER SAYFASI VERİLERİ
if ($page == 'recipes') {
    $recipe_search = $_GET['q'] ?? '';
    $recipe_sql = "SELECT r.*, u.username, c.name as category_name FROM recipes r 
                   LEFT JOIN users u ON r.user_id = u.id 
                   LEFT JOIN categories c ON r.category_id = c.id 
                   WHERE r.title LIKE ? ORDER BY r.created_at DESC";
    $recipes = $pdo->prepare($recipe_sql);
    $recipes->execute(["%$recipe_search%"]);
    $total_recipes = $recipes->rowCount();
}

// C) YORUMLAR SAYFASI VERİLERİ
if ($page == 'comments') {
    $comment_search = $_GET['q'] ?? '';
    $comment_sql = "SELECT c.*, r.title, u.username FROM comments c 
                    JOIN recipes r ON c.recipe_id = r.id 
                    JOIN users u ON c.user_id = u.id 
                    WHERE c.comment LIKE ? OR u.username LIKE ? 
                    ORDER BY c.created_at DESC";
    $comments = $pdo->prepare($comment_sql);
    $comments->execute(["%$comment_search%", "%$comment_search%"]);
    $total_comments = $comments->rowCount();
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Yönetim Paneli</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body { background-color: #f8f9fa; font-family: 'Segoe UI', sans-serif; }
        .sidebar { min-height: 100vh; background: #2c3e50; color: white; width: 250px; position: fixed; left: 0; top: 0; }
        .content { margin-left: 250px; padding: 30px; }
        .nav-link { color: #b0c4de; font-weight: 500; padding: 12px 20px; transition: 0.3s; border-radius: 5px; margin-bottom: 5px; }
        .nav-link:hover, .nav-link.active { background: #34495e; color: #fff; padding-left: 25px; }
        .nav-link i { width: 25px; }
        .card { border: none; box-shadow: 0 4px 6px rgba(0,0,0,0.05); border-radius: 10px; }
        .badge-manager { background: #f39c12; color: #fff; }
        .badge-admin { background: #e74c3c; color: #fff; }
        .badge-user { background: #ecf0f1; color: #333; }
        .stat-card { background: linear-gradient(45deg, #3498db, #2980b9); color: white; padding: 20px; border-radius: 10px; margin-bottom: 20px; }
    </style>
</head>
<body>

<div class="sidebar p-3 d-flex flex-column">
    <h4 class="text-center border-bottom pb-3 mb-4">Admin Paneli</h4>
    
    <div class="mb-4 text-center">
        <div class="fw-bold"><?= htmlspecialchars($_SESSION['username']) ?></div>
        <small class="badge bg-light text-dark"><?= ucfirst($currentUserRole) ?></small>
    </div>

    <ul class="nav flex-column">
        <li class="nav-item">
            <a href="admin.php?page=users" class="nav-link <?= $page == 'users' ? 'active' : '' ?>">
                <i class="fas fa-users"></i> Kullanıcılar
            </a>
        </li>
        <li class="nav-item">
            <a href="admin.php?page=recipes" class="nav-link <?= $page == 'recipes' ? 'active' : '' ?>">
                <i class="fas fa-utensils"></i> Tarifler
            </a>
        </li>
        <li class="nav-item">
            <a href="admin.php?page=comments" class="nav-link <?= $page == 'comments' ? 'active' : '' ?>">
                <i class="fas fa-comments"></i> Yorumlar
            </a>
        </li>
    </ul>

    <div class="mt-auto border-top pt-3">
        <a href="index.php" class="nav-link text-white"><i class="fas fa-home"></i> Siteye Dön</a>
        <a href="logout.php" class="nav-link text-danger"><i class="fas fa-sign-out-alt"></i> Çıkış Yap</a>
    </div>
</div>

<div class="content">
    <?php if(isset($_GET['msg'])): ?>
        <div class="alert alert-success alert-dismissible fade show"><?= htmlspecialchars($_GET['msg']) ?> <button class="btn-close" data-bs-dismiss="alert"></button></div>
    <?php endif; ?>

    <?php if ($page == 'users'): ?>
        <h3 class="mb-4">👥 Kullanıcı Yönetimi</h3>
        
        <div class="stat-card d-flex justify-content-between align-items-center">
            <div>
                <h5 class="mb-0">Toplam Kullanıcı</h5>
                <h2 class="mb-0 fw-bold"><?= $total_users ?></h2>
            </div>
            <i class="fas fa-users fa-3x opacity-50"></i>
        </div>

        <div class="card">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h6 class="mb-0">Kullanıcı Listesi</h6>
                <form class="d-flex" method="GET">
                    <input type="hidden" name="page" value="users">
                    <input type="text" name="q" class="form-control form-control-sm me-2" placeholder="Kullanıcı ara..." value="<?= htmlspecialchars($user_search) ?>">
                    <button class="btn btn-sm btn-primary">Ara</button>
                </form>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light"><tr><th>ID</th><th>Kullanıcı</th><th>E-posta</th><th>Rol</th><th>İşlem</th></tr></thead>
                    <tbody>
                        <?php foreach($users as $u): ?>
                        <tr>
                            <td><?= $u['id'] ?></td>
                            <td class="fw-bold"><?= htmlspecialchars($u['username']) ?></td>
                            <td><?= htmlspecialchars($u['email']) ?></td>
                            <td>
                                <span class="badge badge-<?= $u['role'] ?>"><?= ucfirst($u['role']) ?></span>
                            </td>
                            <td>
                                <?php if($currentUserRole === 'manager' && $u['id'] != $_SESSION['user_id']): ?>
                                    <form method="POST" class="d-inline-flex gap-1 align-items-center">
                                        <input type="hidden" name="user_id" value="<?= $u['id'] ?>">
                                        <select name="role" class="form-select form-select-sm" style="width:auto;">
                                            <option value="user" <?= $u['role']=='user'?'selected':'' ?>>Üye</option>
                                            <option value="admin" <?= $u['role']=='admin'?'selected':'' ?>>Admin</option>
                                            <option value="manager" <?= $u['role']=='manager'?'selected':'' ?>>Yönetici</option>
                                        </select>
                                        <button type="submit" name="update_role" class="btn btn-sm btn-outline-success"><i class="fas fa-check"></i></button>
                                    </form>
                                <?php endif; ?>

                                <?php if($u['id'] != $_SESSION['user_id']): ?>
                                    <a href="admin.php?page=users&delete_type=user&id=<?= $u['id'] ?>" onclick="return confirm('Silmek istediğine emin misin?')" class="btn btn-sm btn-outline-danger ms-1"><i class="fas fa-trash"></i></a>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php endif; ?>


    <?php if ($page == 'recipes'): ?>
        <h3 class="mb-4">🍲 Tarif Yönetimi</h3>

        <div class="stat-card d-flex justify-content-between align-items-center" style="background: linear-gradient(45deg, #e67e22, #f39c12);">
            <div>
                <h5 class="mb-0">Toplam Tarif</h5>
                <h2 class="mb-0 fw-bold"><?= $total_recipes ?></h2>
            </div>
            <i class="fas fa-utensils fa-3x opacity-50"></i>
        </div>

        <div class="card">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h6 class="mb-0">Tarif Listesi</h6>
                <form class="d-flex" method="GET">
                    <input type="hidden" name="page" value="recipes">
                    <input type="text" name="q" class="form-control form-control-sm me-2" placeholder="Tarif ara..." value="<?= htmlspecialchars($recipe_search) ?>">
                    <button class="btn btn-sm btn-warning text-white">Ara</button>
                </form>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light"><tr><th>Resim</th><th>Başlık</th><th>Yazar</th><th>Kategori</th><th>İşlem</th></tr></thead>
                    <tbody>
                        <?php foreach($recipes as $r): ?>
                        <tr>
                            <td>
                                <?php $img = !empty($r['image']) && file_exists('uploads/recipes/'.$r['image']) ? 'uploads/recipes/'.$r['image'] : 'https://placehold.co/40'; ?>
                                <img src="<?= $img ?>" width="40" height="40" class="rounded">
                            </td>
                            <td><a href="recipe.php?id=<?= $r['id'] ?>" target="_blank" class="text-dark fw-bold text-decoration-none"><?= htmlspecialchars($r['title']) ?></a></td>
                            <td><?= htmlspecialchars($r['username']) ?></td>
                            <td><span class="badge bg-info text-dark"><?= htmlspecialchars($r['category_name']) ?></span></td>
                            <td>
                                <a href="admin.php?page=recipes&delete_type=recipe&id=<?= $r['id'] ?>" onclick="return confirm('Bu tarifi silmek istiyor musunuz?')" class="btn btn-sm btn-outline-danger"><i class="fas fa-trash"></i> Sil</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php endif; ?>


    <?php if ($page == 'comments'): ?>
        <h3 class="mb-4">💬 Yorum Yönetimi</h3>

        <div class="stat-card d-flex justify-content-between align-items-center" style="background: linear-gradient(45deg, #9b59b6, #8e44ad);">
            <div>
                <h5 class="mb-0">Toplam Yorum</h5>
                <h2 class="mb-0 fw-bold"><?= $total_comments ?></h2>
            </div>
            <i class="fas fa-comments fa-3x opacity-50"></i>
        </div>

        <div class="card">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h6 class="mb-0">Son Yorumlar</h6>
                <form class="d-flex" method="GET">
                    <input type="hidden" name="page" value="comments">
                    <input type="text" name="q" class="form-control form-control-sm me-2" placeholder="Yorum ara..." value="<?= htmlspecialchars($comment_search) ?>">
                    <button class="btn btn-sm btn-secondary">Ara</button>
                </form>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light"><tr><th>Yorum</th><th>Tarif</th><th>Yazan</th><th>Tarih</th><th>İşlem</th></tr></thead>
                    <tbody>
                        <?php foreach($comments as $c): ?>
                        <tr>
                            <td style="max-width:300px;"><?= htmlspecialchars(mb_substr($c['comment'], 0, 60)) ?>...</td>
                            <td><a href="recipe.php?id=<?= $c['recipe_id'] ?>" target="_blank"><?= htmlspecialchars($c['title']) ?></a></td>
                            <td><?= htmlspecialchars($c['username']) ?></td>
                            <td class="text-muted small"><?= date('d.m.Y H:i', strtotime($c['created_at'])) ?></td>
                            <td>
                                <a href="admin.php?page=comments&delete_type=comment&id=<?= $c['id'] ?>" onclick="return confirm('Bu yorumu silmek istiyor musunuz?')" class="btn btn-sm btn-outline-danger"><i class="fas fa-trash"></i> Sil</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php endif; ?>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>