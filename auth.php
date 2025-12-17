<?php
// auth.php
require_once 'db.php';
session_start();

header('Content-Type: application/json');

$action = $_POST['action'] ?? '';

// --- 1. KAYIT OLMA İŞLEMİ ---
if ($action === 'register') {
    $name = trim($_POST['fullname']); // Ad Soyad
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    // E-posta standardı kontrolü (.com bitişli)
    if (!filter_var($email, FILTER_VALIDATE_EMAIL) || !preg_match('/\.com$/', $email)) {
        echo json_encode(['status' => 'error', 'message' => 'Lütfen geçerli bir e-posta adresi giriniz (@ornek.com).']);
        exit;
    }

    // E-posta veritabanında var mı?
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->rowCount() > 0) {
        echo json_encode(['status' => 'error', 'message' => 'Bu e-posta adresi zaten kayıtlı.']);
        exit;
    }

    // Kayıt ekleme (Varsayılan rol: user)
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, 'user')");
    if ($stmt->execute([$name, $email, $hashedPassword])) {
        echo json_encode(['status' => 'success', 'message' => 'Kayıt başarılı! Şimdi giriş yapabilirsiniz.']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Kayıt sırasında bir hata oluştu.']);
    }
}

// --- 2. GİRİŞ YAPMA İŞLEMİ ---
elseif ($action === 'login') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        // Session'a kullanıcı bilgilerini VE ROLÜNÜ atıyoruz
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['avatar'] = $user['avatar'];
        $_SESSION['role'] = $user['role']; // <-- EKLENEN KISIM
        
        echo json_encode(['status' => 'success', 'message' => 'Giriş başarılı, yönlendiriliyorsunuz...']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'E-posta veya şifre hatalı.']);
    }
}

// --- 3. ŞİFREMİ UNUTTUM ---
elseif ($action === 'forgot') {
    $email = trim($_POST['email']);
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    
    if ($stmt->rowCount() > 0) {
        $code = rand(100000, 999999);
        $update = $pdo->prepare("UPDATE users SET reset_code = ? WHERE email = ?");
        $update->execute([$code, $email]);
        echo json_encode(['status' => 'success', 'message' => 'Doğrulama kodu e-postanıza gönderildi. (Demo Kod: '.$code.')']); 
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Bu e-posta adresi kayıtlı değil.']);
    }
}
?>