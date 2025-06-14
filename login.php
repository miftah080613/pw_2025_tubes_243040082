<?php
session_start();
include '../config/db.php';

header('Content-Type: application/json');

$response = ['success' => false, 'message' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT id, password FROM users WHERE username=?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->bind_result($id, $hashed);
    $stmt->fetch();

    if (password_verify($password, $hashed)) {
        $_SESSION['user_id'] = $id;
        $response['success'] = true;
        $response['message'] = "Login berhasil!";
    } else {
        $response['message'] = "Username atau password salah!";
    }
} else {
    $response['message'] = "Metode request tidak valid.";
}

echo json_encode($response);
exit;
?>
