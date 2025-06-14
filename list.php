<?php
session_start();
include '../config/db.php';
include '../includes/header.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: /travel_blog/index.php");
    exit;
}

// Ambil wishlist pengguna
$user_id = $_SESSION['user_id'];
$query = "SELECT w.*, p.title, p.content, u.username, p.id as post_id, p.image 
          FROM wishlist w 
          JOIN posts p ON w.post_id = p.id 
          JOIN users u ON p.user_id = u.id 
          WHERE w.user_id = ? 
          ORDER BY w.created_at DESC";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<div class="wishlist-page">
    <svg class="wishlist-icon" fill="none" viewBox="0 0 48 48"><path d="M24 44s-16-10.24-16-22A8 8 0 0 1 24 12a8 8 0 0 1 16 10c0 11.76-16 22-16 22Z" stroke="#222" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></svg>
    <div class="wishlist-title">My Wishlist</div>
    <table class="wishlist-table">
        <thead>
            <tr>
                <th></th>
                <th>Judul Postingan</th>
                <th>Ditambahkan Pada</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
        <?php if ($result->num_rows > 0): ?>
            <?php while($item = $result->fetch_assoc()): ?>
                <tr>
                    <td>
                        <form method="post" action="remove.php" style="display:inline;">
                            <input type="hidden" name="id" value="<?= $item['id'] ?>">
                            <button type="submit" class="wishlist-remove" title="Hapus dari wishlist">
                                <svg width="22" height="22" fill="none" viewBox="0 0 24 24"><path d="M6 6l12 12M6 18L18 6" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
                            </button>
                        </form>
                    </td>
                    <td style="display:flex;align-items:center;gap:12px;">
                        <?php if (!empty($item['image'])): ?>
                            <img class="wishlist-img" src="/travel_blog/assets/uploads/<?= htmlspecialchars($item['image']) ?>" alt="Gambar Postingan">
                        <?php else: ?>
                            <img class="wishlist-img" src="https://source.unsplash.com/80x80/?travel,landscape" alt="Gambar">
                        <?php endif; ?>
                        <div>
                            <div style="font-weight:600;"> <?= htmlspecialchars($item['title']) ?> </div>
                            <div style="font-size:0.97em;color:#888;">Oleh: <?= htmlspecialchars($item['username']) ?></div>
                        </div>
                    </td>
                    <td class="wishlist-date">
                        <?= date('F d, Y', strtotime($item['created_at'])) ?>
                    </td>
                    <td>
                        <button class="wishlist-add-btn" disabled>Wishlist</button>
                    </td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr><td colspan="4" style="text-align:center;color:#888;font-style:italic;">Belum ada destinasi dalam wishlist Anda.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>

<?php include '../includes/footer.php'; ?>
