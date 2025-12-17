<?php
// profil_edit.php - Profil Düzenleme ve Doğrulama
require_once 'db.php';
session_start();

// Giriş kontrolü
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$message = '';
$message_type = ''; // success veya danger
$step = isset($_SESSION['update_step']) ? $_SESSION['update_step'] : 1; // 1: Form, 2: OTP Doğrulama

// Mevcut kullanıcı bilgilerini çek
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$currentUser = $stmt->fetch(PDO::FETCH_ASSOC);

// --- FORM İŞLEMLERİ ---

// 1. AŞAMA: Form Gönderildi (Doğrulama Kodu Üret)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'request_update') {
    
    $newName = trim($_POST['username']);
    $newEmail = trim($_POST['email']);
    $currentAvatar = $currentUser['avatar'];
    $newAvatarName = $currentAvatar; // Varsayılan olarak eski resim kalsın

    // 1. E-posta başkası tarafından kullanılıyor mu kontrol et
    $check = $pdo->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
    $check->execute([$newEmail, $user_id]);
    if ($check->rowCount() > 0) {
        $message = "Bu e-posta adresi başka bir üye tarafından kullanılıyor.";
        $message_type = "danger";
    } else {
        // 2. Yeni Resim Yüklendi mi?
        if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = 'uploads/avatars/';
            if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true); // Klasör yoksa oluştur
            
            $fileExt = strtolower(pathinfo($_FILES['avatar']['name'], PATHINFO_EXTENSION));
            $allowed = ['jpg', 'jpeg', 'png', 'webp'];
            
            if (in_array($fileExt, $allowed)) {
                $newFileName = 'user_' . $user_id . '_' . uniqid() . '.' . $fileExt;
                if (move_uploaded_file($_FILES['avatar']['tmp_name'], $uploadDir . $newFileName)) {
                    $newAvatarName = $newFileName;
                }
            } else {
                $message = "Sadece JPG, PNG ve WEBP formatları kabul edilir.";
                $message_type = "danger";
            }
        }

        // Hata yoksa doğrulama kodunu oluştur
        if (empty($message)) {
            $otpCode = rand(100000, 999999);
            
            // Verileri Session'da geçici sakla
            $_SESSION['temp_update'] = [
                'username' => $newName,
                'email' => $newEmail,
                'avatar' => $newAvatarName,
                'otp' => $otpCode
            ];
            
            // Kodu TXT dosyasına yaz (Simüle edilmiş Mail)
            $logMessage = "[" . date('Y-m-d H:i:s') . "] Kullanıcı: $newName | Kod: $otpCode \n";
            file_put_contents('mail_logs.txt', $logMessage, FILE_APPEND);

            $_SESSION['update_step'] = 2; // Doğrulama ekranına geç
            header("Location: profil_edit.php"); // Sayfayı yenile
            exit;
        }
    }
}

// 2. AŞAMA: Doğrulama Kodu Girildi (Güncellemeyi Yap)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'verify_otp') {
    $enteredCode = $_POST['otp_code'];
    
    if (isset($_SESSION['temp_update']) && $enteredCode == $_SESSION['temp_update']['otp']) {
        // Kod Doğru: Veritabanını Güncelle
        $temp = $_SESSION['temp_update'];
        
        $updateStmt = $pdo->prepare("UPDATE users SET username = ?, email = ?, avatar = ? WHERE id = ?");
        $result = $updateStmt->execute([$temp['username'], $temp['email'], $temp['avatar'], $user_id]);

        if ($result) {
            // Sessiondaki bilgileri de güncelle (Anlık değişim için)
            $_SESSION['username'] = $temp['username'];
            $_SESSION['avatar'] = $temp['avatar']; // Oturumdaki avatarı güncelle
            
            // Temizlik
            unset($_SESSION['temp_update']);
            unset($_SESSION['update_step']);
            
            // Başarılı sayfasına yönlendir veya mesaj göster
            echo "<script>alert('Profiliniz başarıyla güncellendi!'); window.location.href='profil.php';</script>";
            exit;
        } else {
            $message = "Veritabanı hatası oluştu.";
            $message_type = "danger";
        }
    } else {
        $message = "Hatalı doğrulama kodu!";
        $message_type = "danger";
    }
}

// İptal Butonu
if (isset($_GET['cancel'])) {
    unset($_SESSION['temp_update']);
    unset($_SESSION['update_step']);
    header("Location: profil.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Profili Düzenle | Lezzet Bahçesi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white p-3">
                    <h4 class="mb-0 text-center" style="color: #ffa07a;">Profili Düzenle</h4>
                </div>
                <div class="card-body p-4">

                    <?php if ($message): ?>
                        <div class="alert alert-<?php echo $message_type; ?>"><?php echo $message; ?></div>
                    <?php endif; ?>

                    <?php if ($step == 1): ?>
                    <form method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="action" value="request_update">
                        
                        <div class="text-center mb-4">
                            <img src="uploads/avatars/<?php echo htmlspecialchars($currentUser['avatar']); ?>" 
                                 class="rounded-circle border" width="100" height="100" style="object-fit: cover;">
                            <div class="mt-2">
                                <label for="avatar" class="btn btn-sm btn-outline-secondary">Fotoğrafı Değiştir</label>
                                <input type="file" name="avatar" id="avatar" class="d-none" accept="image/*" onchange="previewImage(this)">
                            </div>
                            <small class="text-muted d-block mt-1" id="file-name"></small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Ad Soyad</label>
                            <input type="text" name="username" class="form-control" value="<?php echo htmlspecialchars($currentUser['username']); ?>" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">E-Posta Adresi</label>
                            <input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($currentUser['email']); ?>" required>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn text-white" style="background-color: #ffa07a;">Güncelle ve Doğrula</button>
                            <a href="profil.php" class="btn btn-light">İptal</a>
                        </div>
                    </form>
                    
                    <?php elseif ($step == 2): ?>
                    <div class="text-center">
                        <div class="alert alert-info">
                            <i class="fas fa-envelope"></i> Güvenlik kodunuz <strong>mail_logs.txt</strong> dosyasına gönderildi.
                        </div>
                        <form method="POST">
                            <input type="hidden" name="action" value="verify_otp">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Doğrulama Kodu</label>
                                <input type="number" name="otp_code" class="form-control text-center fs-4" placeholder="******" required>
                            </div>
                            <button type="submit" class="btn btn-success w-100 mb-2">Onayla</button>
                            <a href="profil_edit.php?cancel=1" class="btn btn-outline-danger w-100">İptal Et</a>
                        </form>
                    </div>
                    <?php endif; ?>

                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Seçilen dosya ismini göster
    function previewImage(input) {
        if (input.files && input.files[0]) {
            document.getElementById('file-name').innerText = "Seçilen: " + input.files[0].name;
        }
    }
</script>

</body>
</html>