<?php
session_start();
include '../config/db.php';

if (!isset($_SESSION['user_id'])) {
    die("Login dulu untuk menyimpan wishlist.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['post_id'])) {
    $user_id = $_SESSION['user_id'];
    $post_id = intval($_POST['post_id']);

    // Cek apakah sudah ada di wishlist
    $cek = $conn->prepare("SELECT id FROM wishlist WHERE user_id=? AND post_id=?");
    $cek->bind_param("ii", $user_id, $post_id);
    $cek->execute();
    $cek->store_result();

    if ($cek->num_rows == 0) {
        $stmt = $conn->prepare("INSERT INTO wishlist (user_id, post_id) VALUES (?, ?)");
        $stmt->bind_param("ii", $user_id, $post_id);
        $stmt->execute();
    }
    // Redirect kembali ke halaman sebelumnya
    header("Location: " . $_SERVER['HTTP_REFERER']);
    exit;
} else {
    echo "Permintaan tidak valid.";
}
?>
