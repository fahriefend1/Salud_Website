<?php
require_once 'database.php';

function trackVisit() {
    global $pdo;
    
    // Skip tracking jika tidak ada session atau halaman tertentu
    if (isset($_SESSION['admin_id']) || 
        strpos($_SERVER['REQUEST_URI'], '/admin/') !== false ||
        strpos($_SERVER['REQUEST_URI'], 'contact.php') !== false ||
        strpos($_SERVER['REQUEST_URI'], 'order.php') !== false) {
        return;
    }
    
    $ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
    $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown';
    
    try {
        // Cache check untuk mengurangi query
        $cacheKey = 'visit_' . md5($ip . date('Y-m-d-H'));
        if (isset($_SESSION[$cacheKey])) {
            return;
        }
        
        // Cek apakah sudah ada kunjungan dari IP ini dalam 24 jam terakhir
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM visits WHERE ip_address = ? AND visited_at > DATE_SUB(NOW(), INTERVAL 24 HOUR)");
        $stmt->execute([$ip]);
        $count = $stmt->fetchColumn();
        
        if ($count == 0) {
            // Simpan kunjungan
            $stmt = $pdo->prepare("INSERT INTO visits (ip_address, user_agent) VALUES (?, ?)");
            $stmt->execute([$ip, $userAgent]);
            
            // Set cache untuk 1 jam
            $_SESSION[$cacheKey] = true;
        }
    } catch (PDOException $e) {
        // Silent error - jangan ganggu user
        error_log("Track visit error: " . $e->getMessage());
    }
}

function formatDate($date) {
    return date('d M Y', strtotime($date));
}

function formatPrice($price) {
    return 'Rp ' . number_format($price, 0, ',', '.');
}

function getProductImage($image) {
    if ($image && file_exists("assets/uploads/$image")) {
        return "assets/uploads/$image";
    }
    return "assets/images/default.jpg";
}
?>