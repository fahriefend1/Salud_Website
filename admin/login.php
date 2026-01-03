<?php
require_once '../includes/config.php';
require_once '../includes/database.php';

// Jika sudah login, redirect ke dashboard
if (isset($_SESSION['admin_id'])) {
    header('Location: dashboard.php');
    exit();
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    $remember = isset($_POST['remember']);
    
    if (empty($username) || empty($password)) {
        $error = 'Username dan password harus diisi!';
    } else {
        $stmt = $pdo->prepare("SELECT * FROM admin WHERE username = ?");
        $stmt->execute([$username]);
        $admin = $stmt->fetch();
        
        if ($admin && password_verify($password, $admin['password'])) {
            // Set session
            $_SESSION['admin_id'] = $admin['id'];
            $_SESSION['admin_name'] = $admin['username'];
            
            // Set cookie jika remember di checklist
            if ($remember) {
                setcookie('admin_user', $username, time() + (86400 * 30), "/");
                setcookie('admin_pass', $password, time() + (86400 * 30), "/");
            }
            
            header('Location: dashboard.php');
            exit();
        } else {
            $error = 'Username atau password salah!';
        }
    }
}

// Cek cookie untuk auto-fill
$cookie_user = $_COOKIE['admin_user'] ?? '';
$cookie_pass = $_COOKIE['admin_pass'] ?? '';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - SALUD</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@600;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: 'Inter', sans-serif; 
            background: linear-gradient(135deg, #FFF4D9, #FDC64E);
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-container {
            background: white;
            padding: 40px;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            width: 100%;
            max-width: 400px;
        }
        .logo {
            text-align: center;
            margin-bottom: 30px;
        }
        .logo h1 {
            color: #FDC64E;
            font-size: 2.5rem;
            font-family: 'Poppins', sans-serif;
        }
        .logo p {
            color: #666;
            font-size: 0.9rem;
        }
        .input-group {
            margin-bottom: 20px;
        }
        .input-group label {
            display: block;
            margin-bottom: 8px;
            color: #333;
            font-weight: 500;
        }
        .input-group input {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #ddd;
            border-radius: 10px;
            font-size: 1rem;
            transition: border 0.3s;
        }
        .input-group input:focus {
            border-color: #FDC64E;
            outline: none;
        }
        .remember {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
        }
        .remember input {
            margin-right: 10px;
        }
        .btn-login {
            width: 100%;
            padding: 14px;
            background: #FDC64E;
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.3s;
        }
        .btn-login:hover {
            background: #e6b347;
        }
        .error {
            background: #ffebee;
            color: #c62828;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 20px;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="logo">
            <h1>SALUD</h1>
            <p>Admin Login - Puding Salad Segar & Seru!</p>
        </div>
        
        <?php if ($error): ?>
            <div class="error"><?= $error ?></div>
        <?php endif; ?>
        
        <form method="POST">
            <div class="input-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" 
                       value="<?= $cookie_user ?>" 
                       required>
            </div>
            
            <div class="input-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" 
                       value="<?= $cookie_pass ?>" 
                       required>
            </div>
            
            <div class="remember">
                <input type="checkbox" id="remember" name="remember" 
                       <?= $cookie_user ? 'checked' : '' ?>>
                <label for="remember">Ingat saya</label>
            </div>
            
            <button type="submit" name="login" class="btn-login">Sign In</button>
        </form>
    </div>
</body>
</html>