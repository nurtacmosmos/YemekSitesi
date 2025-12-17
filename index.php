<?php
// index.php - HATASIZ SÜRÜM
require_once 'db.php';
// Oturumu header.php zaten başlatacak

// --- YARDIMCI FONKSİYON: RESİM BULUCU ---
function getRecipeImage($imageName) {
    if (!empty($imageName) && file_exists('uploads/recipes/' . $imageName)) {
        return 'uploads/recipes/' . $imageName;
    }
    return 'https://placehold.co/600x400/ffd1dc/white?text=Lezzet+Bahcesi';
}

// --- FİLTRELEME MANTIĞI ---
$is_search_mode = false;
$search_results = [];
$search_title = "";

if (isset($_GET['q']) || isset($_GET['cat'])) {
    $is_search_mode = true;
    $sql = "SELECT r.*, u.username, c.name as category_name FROM recipes r JOIN users u ON r.user_id = u.id JOIN categories c ON r.category_id = c.id WHERE 1=1";
    $params = [];
    if (!empty($_GET['q'])) {
        $sql .= " AND (r.title LIKE ? OR r.description LIKE ?)";
        $params[] = "%".$_GET['q']."%"; $params[] = "%".$_GET['q']."%";
        $search_title .= "Aranan: '<b>" . htmlspecialchars($_GET['q']) . "</b>' ";
    }
    if (!empty($_GET['cat'])) {
        $sql .= " AND r.category_id = ?";
        $params[] = $_GET['cat'];
    }
    $sql .= " ORDER BY r.created_at DESC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $search_results = $stmt->fetchAll(PDO::FETCH_ASSOC);
} else {
    // STANDART VERİLER
    try {
        $heroRecipes = $pdo->query("SELECT * FROM recipes ORDER BY RAND() LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);
        $popularRecipes = $pdo->query("SELECT * FROM recipes ORDER BY views DESC LIMIT 10")->fetchAll(PDO::FETCH_ASSOC);
        $newRecipes = $pdo->query("SELECT * FROM recipes ORDER BY created_at DESC LIMIT 10")->fetchAll(PDO::FETCH_ASSOC);
        $topEditors = $pdo->query("SELECT users.*, COUNT(recipes.id) as recipe_count FROM users JOIN recipes ON users.id = recipes.user_id GROUP BY users.id ORDER BY recipe_count DESC LIMIT 10")->fetchAll(PDO::FETCH_ASSOC);
    } catch(PDOException $e) {}
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Lezzet Bahçesi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Pacifico&family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        body { background-color: #fff8f6; font-family: 'Poppins', sans-serif; color: #4a4a4a; }
        .hero-swiper { width: 100%; height: 500px; margin-bottom: 3rem; }
        .hero-slide-content { width: 100%; height: 100%; background-size: cover; background-position: center; display: flex; align-items: center; justify-content: center; position: relative; }
        .hero-slide-content::before { content: ''; position: absolute; top:0; left:0; right:0; bottom:0; background: rgba(0,0,0,0.2); }
        .hero-overlay { position: relative; z-index: 2; background: rgba(255, 255, 255, 0.9); padding: 2rem 3rem; border-radius: 20px; text-align: center; }
        .recipe-card { background: #fff; border-radius: 15px; overflow: hidden; border: none; box-shadow: 0 5px 15px rgba(0,0,0,0.03); transition: transform 0.3s; height: 100%; display: flex; flex-direction: column; }
        .recipe-card:hover { transform: translateY(-5px); }
        .card-img-top { height: 200px; object-fit: cover; }
        .editor-img { width: 100px; height: 100px; border-radius: 50%; object-fit: cover; border: 3px solid #ffdab9; }
        .search-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 20px; }
        .swiper-button-next, .swiper-button-prev { color: #ffa07a; }
    </style>
</head>
<body>

    <?php include 'header.php'; ?>

    <?php if($is_search_mode): ?>
        <div class="container my-5" style="min-height: 500px;">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h3 class="fw-bold">🔍 Sonuçlar</h3>
                <span class="text-muted"><?= $search_title ?></span>
                <a href="index.php" class="btn btn-sm btn-outline-secondary">Temizle</a>
            </div>
            <?php if(count($search_results) > 0): ?>
                <div class="search-grid">
                    <?php foreach($search_results as $res): ?>
                        <div class="recipe-card border shadow-sm">
                            <img src="<?= getRecipeImage($res['image']) ?>" class="card-img-top">
                            <div class="card-body text-center">
                                <h5 class="fw-bold mb-3"><?= htmlspecialchars($res['title']) ?></h5>
                                <a href="recipe.php?id=<?= $res['id'] ?>" class="btn btn-sm w-100 text-white" style="background-color: #ffa07a;">Tarifi Gör</a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="alert alert-warning text-center">Sonuç bulunamadı.</div>
            <?php endif; ?>
        </div>
    <?php else: ?>
        
        <?php if(!empty($heroRecipes)): ?>
        <div class="swiper hero-swiper">
            <div class="swiper-wrapper">
                <?php foreach($heroRecipes as $hero): ?>
                <div class="swiper-slide">
                    <a href="recipe.php?id=<?= $hero['id'] ?>">
                        <div class="hero-slide-content" style="background-image: url('<?= getRecipeImage($hero['image']) ?>');">
                            <div class="hero-overlay">
                                <h2 class="hero-title" style="font-family:'Pacifico'; color:#ffa07a; font-size:2.5rem;"><?= htmlspecialchars($hero['title']) ?></h2>
                                <span class="btn mt-3 text-white px-4" style="background-color: #ffa07a;">Tarife Git</span>
                            </div>
                        </div>
                    </a>
                </div>
                <?php endforeach; ?>
            </div>
            <div class="swiper-pagination"></div>
        </div>
        <?php endif; ?>

        <div class="container mb-5">
            <h3 id="populer" class="fw-bold mb-3" style="border-left: 5px solid #ffa07a; padding-left: 15px;">🔥 Popüler Tarifler</h3>
            <div class="swiper card-swiper mb-5">
                <div class="swiper-wrapper">
                    <?php foreach($popularRecipes as $pop): ?>
                    <div class="swiper-slide">
                        <div class="recipe-card">
                            <img src="<?= getRecipeImage($pop['image']) ?>" class="card-img-top">
                            <div class="card-body text-center">
                                <h5 class="fw-bold"><?= htmlspecialchars($pop['title']) ?></h5>
                                <a href="recipe.php?id=<?= $pop['id'] ?>" class="btn btn-sm w-100 mt-2" style="background-color: #ffd1dc;">İncele</a>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <div class="swiper-button-next"></div><div class="swiper-button-prev"></div>
            </div>

            <h3 id="yeni" class="fw-bold mb-3" style="border-left: 5px solid #ffa07a; padding-left: 15px;">🆕 Yeni Tarifler</h3>
            <div class="swiper card-swiper mb-5">
                <div class="swiper-wrapper">
                    <?php foreach($newRecipes as $new): ?>
                    <div class="swiper-slide">
                        <div class="recipe-card">
                            <img src="<?= getRecipeImage($new['image']) ?>" class="card-img-top">
                            <div class="card-body text-center">
                                <h5 class="fw-bold"><?= htmlspecialchars($new['title']) ?></h5>
                                <a href="recipe.php?id=<?= $new['id'] ?>" class="btn btn-sm btn-outline-secondary w-100 mt-2">Git</a>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            
            <h3 class="fw-bold mb-3" style="border-left: 5px solid #b0e0e6; padding-left: 15px;">✨ Popüler Editörler</h3>
            <div class="swiper editor-swiper text-center pb-4">
                <div class="swiper-wrapper">
                    <?php foreach($topEditors as $editor): ?>
                    <div class="swiper-slide editor-slide">
                        <a href="profil.php?id=<?= $editor['id'] ?>" class="text-decoration-none text-dark">
                            <?php 
                            $edAvatar = !empty($editor['avatar']) && file_exists('uploads/avatars/'.$editor['avatar']) ? 'uploads/avatars/'.$editor['avatar'] : 'https://placehold.co/100x100/ffdab9/white?text=Ed'; 
                            ?>
                            <img src="<?= $edAvatar ?>" class="editor-img">
                            <h6 class="fw-bold mt-2"><?= htmlspecialchars($editor['username']) ?></h6>
                            <small class="text-muted"><?= $editor['recipe_count'] ?> Tarif</small>
                        </a>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <div class="modal fade" id="authModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="border-radius: 15px; border:none;">
                <div class="modal-header" style="background-color: #ffd1dc;">
                    <h5 class="modal-title" id="modalTitle">Giriş Yap</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div id="authAlert" class="alert d-none"></div>
                    <form id="loginForm">
                        <div class="mb-3"><input type="email" name="email" class="form-control" placeholder="E-posta" required></div>
                        <div class="mb-3"><input type="password" name="password" class="form-control" placeholder="Şifre" required></div>
                        <button type="submit" class="btn w-100 text-white" style="background-color: #ffa07a;">Giriş Yap</button>
                        <div class="text-center mt-3"><span class="small" onclick="switchView('register')" style="cursor:pointer; color:#ffa07a;">Kayıt Ol</span></div>
                    </form>
                    <form id="registerForm" class="d-none">
                        <div class="mb-3"><input type="text" name="fullname" class="form-control" placeholder="Ad Soyad" required></div>
                        <div class="mb-3"><input type="email" name="email" class="form-control" placeholder="E-posta" required></div>
                        <div class="mb-3"><input type="password" name="password" class="form-control" placeholder="Şifre" required></div>
                        <button type="submit" class="btn w-100 text-white" style="background-color: #ffa07a;">Kayıt Ol</button>
                        <div class="text-center mt-3"><span class="small" onclick="switchView('login')" style="cursor:pointer; color:#ffa07a;">Giriş Yap</span></div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.js"></script>
    <script>
        const heroSwiper = new Swiper('.hero-swiper', { loop: true, autoplay: { delay: 4000 }, pagination: { el: '.swiper-pagination', clickable: true } });
        new Swiper('.card-swiper', { slidesPerView: 1, spaceBetween: 20, loop: true, navigation: { nextEl: '.swiper-button-next', prevEl: '.swiper-button-prev' }, breakpoints: { 576: { slidesPerView: 2 }, 768: { slidesPerView: 3 }, 1200: { slidesPerView: 4 } } });
        new Swiper('.editor-swiper', { slidesPerView: 3, spaceBetween: 20, loop: true, autoplay: { delay: 3000 }, breakpoints: { 576: { slidesPerView: 4 }, 768: { slidesPerView: 6 }, 1024: { slidesPerView: 8 } } });

        function switchView(view) {
            document.getElementById('loginForm').classList.add('d-none');
            document.getElementById('registerForm').classList.add('d-none');
            document.getElementById(view + 'Form').classList.remove('d-none');
            document.getElementById('modalTitle').innerText = view === 'login' ? 'Giriş Yap' : 'Kayıt Ol';
        }
        
        async function handleAuth(event, action, formId) {
            event.preventDefault();
            const formData = new FormData(document.getElementById(formId));
            formData.append('action', action);
            const alertBox = document.getElementById('authAlert');
            try {
                const response = await fetch('auth.php', { method: 'POST', body: formData });
                const result = await response.json();
                alertBox.classList.remove('d-none');
                alertBox.className = result.status === 'success' ? 'alert alert-success' : 'alert alert-danger';
                alertBox.innerText = result.message;
                if(result.status === 'success' && action === 'login') setTimeout(() => location.reload(), 1500);
            } catch (e) { console.error(e); }
        }
        document.getElementById('loginForm').addEventListener('submit', (e) => handleAuth(e, 'login', 'loginForm'));
        document.getElementById('registerForm').addEventListener('submit', (e) => handleAuth(e, 'register', 'registerForm'));
    </script>
</body>
</html>