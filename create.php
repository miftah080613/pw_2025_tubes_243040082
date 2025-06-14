<?php
session_start();
include '../config/db.php';
include '../includes/header.php';

if (!isset($_SESSION['user_id'])) {
    die("Harus login dulu.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $content = $_POST['content'];
    $user_id = $_SESSION['user_id'];
    $image = null;

    // Handle upload gambar
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $newName = 'post_' . time() . '_' . rand(1000,9999) . '.' . $ext;
        $uploadDir = __DIR__ . '/../assets/uploads/';
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
        $uploadPath = $uploadDir . $newName;
        if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadPath)) {
            $image = $newName;
        }
    }

    $stmt = $conn->prepare("INSERT INTO posts (user_id, title, image, content) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("isss", $user_id, $title, $image, $content);
    $stmt->execute();
    echo "<p>Post berhasil dibuat!</p>";
    exit;
}
?>
<h2>Buat Post Destinasi</h2>
<form method="post" enctype="multipart/form-data">
    Judul: <input name="title" required>
    Konten: <textarea name="content" required></textarea>
    Gambar: <input type="file" name="image">
    <button type="submit">Buat Post</button>
</form>
<?php include '../includes/footer.php'; ?>
