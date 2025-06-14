<?php
session_start();
include '../config/db.php';

header('Content-Type: application/json');

$response = ['success' => false, 'message' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    // Cek apakah username sudah ada
    $check = $conn->prepare("SELECT id FROM users WHERE username=?");
    $check->bind_param("s", $username);
    $check->execute();
    $check->store_result();

    if ($check->num_rows > 0) {
        $response['message'] = "Username sudah digunakan!";
    } else {
        $stmt = $conn->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
        $stmt->bind_param("ss", $username, $password);
        
        if ($stmt->execute()) {
            $response['success'] = true;
            $response['message'] = "Pendaftaran berhasil! Silakan login.";
        } else {
            $response['message'] = "Terjadi kesalahan saat mendaftar.";
        }
    }
} else {
    $response['message'] = "Metode request tidak valid.";
}

echo json_encode($response);
exit;
?>
