<?php
session_start();
include '../config/db.php';
include '../includes/header.php';

if (!isset($_GET['id'])) {
    echo '<p>Postingan tidak ditemukan.</p>';
    include '../includes/footer.php';
    exit;
}
$id = intval($_GET['id']);
$stmt = $conn->prepare("SELECT p.*, u.username FROM posts p JOIN users u ON p.user_id = u.id WHERE p.id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows == 0) {
    echo '<p>Postingan tidak ditemukan.</p>';
    include '../includes/footer.php';
    exit;
}
$post = $result->fetch_assoc();
?>
<div class="detail-container" style="max-width:700px;margin:40px auto 40px auto;background:#fff;padding:32px 28px 24px 28px;border-radius:16px;box-shadow:0 4px 24px rgba(0,0,0,0.08);">
    <?php if (!empty($post['image'])): ?>
        <img src="/travel_blog/assets/uploads/<?= htmlspecialchars($post['image']) ?>" alt="Gambar Postingan" style="width:100%;max-height:340px;object-fit:cover;border-radius:12px;">
    <?php else: ?>
        <img src="https://source.unsplash.com/800x340/?travel,beach,<?= urlencode($post['title']) ?>" alt="Gambar Postingan" style="width:100%;max-height:340px;object-fit:cover;border-radius:12px;">
    <?php endif; ?>
    <h2 style="margin-top:24px;"> <?= htmlspecialchars($post['title']) ?> </h2>
    <div style="color:#555;font-size:1.05em;margin-bottom:18px;">
        Oleh: <b><?= htmlspecialchars($post['username']) ?></b> &nbsp; | &nbsp; <?= date('d M Y', strtotime($post['created_at'])) ?>
    </div>
    <div style="line-height:1.7;font-size:1.13em;color:#222;white-space:pre-line;">
        <?= nl2br(htmlspecialchars($post['content'])) ?>
    </div>
</div>
<?php include '../includes/footer.php'; ?> 