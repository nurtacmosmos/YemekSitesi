<?php
// header.php - %100 RESPONSIVE VERSİYON
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'db.php';

// Resim Bulucu
if (!function_exists('getRecipeImage')) {
    function getRecipeImage($imageName) {
        if (!empty($imageName) && file_exists('uploads/recipes/' . $imageName)) {
            return 'uploads/recipes/' . $imageName;
        }
        return 'https://placehold.co/600x400/ffd1dc/white?text=Lezzet+Bahcesi';
    }
}

// Kategorileri Çek
$header_categories = [];
try {
    $catQuery = $pdo->query("SELECT * FROM categories ORDER BY name ASC");
    if($catQuery) $header_categories = $catQuery->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lezzet Bahçesi</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Pacifico&family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    
    <style>
        /* --- GENEL TASARIM --- */
        :root { --powder-blue: #b0e0e6; --pastel-pink: #ffd1dc; --light-salmon: #ffa07a; --peach-puff: #ffdab9; --snow-white: #fff8f6; --text-dark: #4a4a4a; }
        body { background-color: var(--snow-white); font-family: 'Poppins', sans-serif; color: var(--text-dark); display: flex; flex-direction: column; min-height: 100vh; }
        
        /* Navbar */
        .navbar { background-color: #fff; box-shadow: 0 2px 10px rgba(0,0,0,0.05); padding: 0.8rem 0; }
        .navbar-brand { font-family: 'Pacifico', cursive; font-size: 1.8rem; color: var(--light-salmon) !important; text-shadow: 1px 1px 2px rgba(0,0,0,0.1); }
        
        /* Responsive Arama Çubuğu */
        .search-bar { 
            border: 2px solid var(--peach-puff); 
            border-radius: 20px; 
            padding: 5px 15px; 
            width: 100%; /* Mobilde tam genişlik */
            max-width: 300px; /* Masaüstünde sınırla */
            transition: all 0.3s; 
        }
        .search-bar:focus { outline: none; border-color: var(--light-salmon); box-shadow: 0 0 5px var(--pastel-pink); }
        
        .nav-link { color: var(--text-dark); font-weight: 600; font-size: 0.95rem; }
        .nav-link:hover { color: var(--light-salmon); }

        /* Mobilde Menü Düzenlemeleri */
        @media (max-width: 991px) {
            .navbar-collapse {
                background-color: #fff;
                padding: 1rem;
                margin-top: 10px;
                border-radius: 10px;
                box-shadow: 0 10px 30px rgba(0,0,0,0.05);
            }
            .navbar-nav { gap: 10px; margin-bottom: 15px; }
            .auth-buttons {
                flex-direction: column;
                width: 100%;
                gap: 10px !important;
            }
            .btn-auth-mobile { width: 100%; text-align: center; }
            .search-container { width: 100%; margin-bottom: 10px; }
            .search-bar { max-width: 100%; } /* Mobilde tam genişlik */
        }

        /* Kartlar ve Sliderlar */
        .hero-swiper { width: 100%; height: 500px; margin-bottom: 3rem; }
        .hero-slide-content { width: 100%; height: 100%; background-size: cover; background-position: center; display: flex; align-items: center; justify-content: center; position: relative; }
        .hero-slide-content::before { content: ''; position: absolute; top:0; left:0; right:0; bottom:0; background: rgba(0,0,0,0.2); }
        .hero-overlay { position: relative; z-index: 2; background: rgba(255, 255, 255, 0.9); padding: 2rem 3rem; border-radius: 20px; text-align: center; box-shadow: 0 10px 30px rgba(0,0,0,0.1); max-width: 600px; }
        
        .section-title { border-left: 5px solid var(--light-salmon); padding-left: 15px; margin-bottom: 1.5rem; font-weight: 600; }
        
        .recipe-card { background: #fff; border-radius: 15px; overflow: hidden; border: none; box-shadow: 0 5px 15px rgba(0,0,0,0.03); transition: transform 0.3s; height: 100%; display: flex; flex-direction: column; }
        .recipe-card:hover { transform: translateY(-5px); box-shadow: 0 10px 20px rgba(0,0,0,0.1); }
        .card-img-top { height: 200px; object-fit: cover; }
        
        .editor-img { width: 100px; height: 100px; border-radius: 50%; object-fit: cover; border: 3px solid var(--peach-puff); margin-bottom: 10px; transition: 0.3s; }
        .editor-slide:hover .editor-img { transform: scale(1.1); border-color: var(--light-salmon); }
        
        .swiper-button-next, .swiper-button-prev { color: var(--light-salmon); }
        .search-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 20px; }

        /* Modal */
        .modal-backdrop.show { backdrop-filter: blur(5px); background-color: rgba(0, 0, 0, 0.4); }
        .custom-modal-content { border: none; border-radius: 15px; }
        .modal-header { background-color: var(--pastel-pink); border-bottom: none; }
        .btn-auth { background-color: var(--light-salmon); color: white; border: none; border-radius: 8px; padding: 10px; width: 100%; font-weight: 600; }
        .btn-auth:hover { background-color: #ff8c61; color: white; }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg sticky-top">
    <div class="container">
        <a class="navbar-brand me-4" href="index.php">Lezzet Bahçesi</a>
        
        <button class="navbar-toggler border-0 shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarNav">
            
            <ul class="navbar-nav mx-auto align-items-center w-100 justify-content-center gap-3">
                <li class="nav-item search-container">
                    <form action="index.php" method="GET" class="d-flex">
                        <input type="text" name="q" class="search-bar form-control" placeholder="Tarif ara..." value="<?= isset($_GET['q']) ? htmlspecialchars($_GET['q']) : '' ?>">
                        <?php if(isset($_GET['cat'])): ?><input type="hidden" name="cat" value="<?= htmlspecialchars($_GET['cat']) ?>"><?php endif; ?>
                    </form>
                </li>
                <li class="nav-item"><a class="nav-link" href="index.php#populer">Popüler</a></li>
                <li class="nav-item"><a class="nav-link" href="index.php#yeni">Yeni</a></li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">Kategoriler</a>
                    <ul class="dropdown-menu border-0 shadow">
                        <li><a class="dropdown-item" href="index.php">Tümü</a></li>
                        <?php if($header_categories): foreach($header_categories as $cat): ?>
                            <li><a class="dropdown-item" href="index.php?cat=<?= $cat['id'] ?>"><?= htmlspecialchars($cat['name']) ?></a></li>
                        <?php endforeach; endif; ?>
                    </ul>
                </li>
            </ul>

            <div class="d-flex align-items-center gap-3 auth-buttons mt-3 mt-lg-0">
                <?php if(isset($_SESSION['user_id'])): ?>
                    <a href="recipe_form.php" class="btn btn-sm rounded-pill px-3 py-2 text-white btn-auth-mobile" style="background-color: var(--powder-blue); border:none; color: var(--text-dark) !important;">
                        <i class="fas fa-plus-circle"></i>
                    </a>
                    
                    <a href="profil.php" class="text-decoration-none text-dark d-flex align-items-center gap-2 btn-auth-mobile justify-content-center">
                        <?php 
                            $avatar = !empty($_SESSION['avatar']) ? $_SESSION['avatar'] : 'default.png';
                            $avatarPath = file_exists('uploads/avatars/'.$avatar) ? 'uploads/avatars/'.$avatar : 'https://placehold.co/100x100/ffa07a/white?text=U'; 
                        ?>
                        <img src="<?= $avatarPath ?>" class="rounded-circle border" width="35" height="35" style="object-fit:cover;">
                        <span class="fw-bold d-lg-block"><?= htmlspecialchars($_SESSION['username']) ?></span>
                    </a>
                    
                    <a href="logout.php" class="btn btn-sm btn-outline-danger btn-auth-mobile"><i class="fas fa-sign-out-alt"></i></a>
                <?php else: ?>
                    <button class="btn btn-outline-dark rounded-pill px-4 btn-auth-mobile" style="border-color: var(--light-salmon);" data-bs-toggle="modal" data-bs-target="#authModal">
                        <i class="fas fa-user me-2"></i> Giriş Yap
                    </button>
                <?php endif; ?>
            </div>

        </div>
    </div>
</nav>