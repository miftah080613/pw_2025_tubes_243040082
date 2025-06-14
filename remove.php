<?php
session_start();
include '../config/db.php';

if (!isset($_SESSION['user_id'])) {
    die('Akses ditolak.');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $id = intval($_POST['id']);
    $user_id = $_SESSION['user_id'];
    // Pastikan hanya wishlist milik user yang bisa dihapus
    $stmt = $conn->prepare("DELETE FROM wishlist WHERE id=? AND user_id=?");
    $stmt->bind_param("ii", $id, $user_id);
    $stmt->execute();
}
// Redirect kembali ke halaman sebelumnya
header("Location: " . $_SERVER['HTTP_REFERER']);
exit; 