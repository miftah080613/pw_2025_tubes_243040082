<?php
session_start();
include 'includes/header.php';
include 'config/db.php';

if (isset($_SESSION['error'])) {
    echo '<div class="alert alert-error">' . $_SESSION['error'] . '</div>';
    unset($_SESSION['error']);
}

if (isset($_SESSION['success'])) {
    echo '<div class="alert alert-success">' . $_SESSION['success'] . '</div>';
    unset($_SESSION['success']);
}

// Ambil 3 postingan terbaru untuk homepage
$query_recent = "SELECT p.*, u.username 
                FROM posts p 
                JOIN users u ON p.user_id = u.id 
                ORDER BY p.created_at DESC 
                LIMIT 3";
$result_recent = $conn->query($query_recent);

// Ambil semua post_id yang sudah ada di wishlist user (jika login)
$wishlist_post_ids = [];
if (isset($_SESSION['user_id'])) {
    $uid = $_SESSION['user_id'];
    $wq = $conn->query("SELECT post_id FROM wishlist WHERE user_id = $uid");
    while($w = $wq->fetch_assoc()) $wishlist_post_ids[] = $w['post_id'];
}

?>

<div class="hero-section">
    <div class="hero-content">
        <h1>Temukan Petualanganmu Berikutnya</h1>
        <p>Jelajahi destinasi menakjubkan dan baca kisah perjalanan inspiratif dari seluruh dunia.</p>
        <div class="hero-buttons">
            <?php if (isset($_SESSION['user_id'])): ?>
                <a href="/travel_blog/posts/list.php" class="btn btn-primary">Lihat Semua Postingan</a>
            <?php else: ?>
                <button onclick="openModal('accessRestrictedModal')" class="btn btn-primary">Lihat Semua Postingan</button>
            <?php endif; ?>
            <?php if (!isset($_SESSION['user_id'])): ?>
                <button onclick="openModal('registerModal')" class="btn btn-secondary">Daftar Sekarang</button>
            <?php endif; ?>
        </div>
    </div>
</div>

<div class="recent-posts-section">
    <h2>Postingan Terbaru</h2>
    <div class="posts-grid">
    <?php if ($result_recent->num_rows > 0): ?>
        <?php while($post = $result_recent->fetch_assoc()): ?>
            <div class="post-card">
                <?php if (isset($_SESSION['user_id'])): ?>
                <form method="post" action="wishlist/add.php" style="position:absolute;top:0;right:0;">
                    <input type="hidden" name="post_id" value="<?= $post['id'] ?>">
                    <button type="submit" class="card-bookmark<?= in_array($post['id'], $wishlist_post_ids) ? ' active' : '' ?>" title="Tambah ke Wishlist">
                        <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="#888" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M19 21l-7-5-7 5V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2z"/>
                        </svg>
                    </button>
                </form>
                <?php endif; ?>
                <?php if (!empty($post['image'])): ?>
                    <img class="post-image" src="/travel_blog/assets/uploads/<?= htmlspecialchars($post['image']) ?>" alt="Gambar Postingan">
                <?php else: ?>
                    <img class="post-image" src="https://source.unsplash.com/600x400/?travel,beach,<?= urlencode($post['title']) ?>" alt="Gambar Postingan">
                <?php endif; ?>
                <h3><a href="posts/detail.php?id=<?= $post['id'] ?>" style="text-decoration:none;color:inherit;"><?= htmlspecialchars($post['title']) ?></a></h3>
                <div class="post-meta">
                    <span class="author">Oleh: <?= htmlspecialchars($post['username']) ?></span>
                    <span class="date"><?= date('d M Y', strtotime($post['created_at'])) ?></span>
                </div>
                <div class="post-content">
                    <?php
                    $max = 160;
                    $plain = strip_tags($post['content']);
                    if (mb_strlen($plain) > $max) {
                        echo nl2br(htmlspecialchars(mb_substr($plain,0,$max))) . '...';
                        echo '<br><a href="posts/detail.php?id=' . $post['id'] . '" class="readmore-link">Selengkapnya</a>';
                    } else {
                        echo nl2br(htmlspecialchars($plain));
                    }
                    ?>
                </div>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <p class="no-posts">Belum ada postingan terbaru.</p>
    <?php endif; ?>
    </div>
    <div class="view-all-posts">
        <?php if (isset($_SESSION['user_id'])): ?>
            <a href="/travel_blog/posts/list.php" class="btn btn-outline">Lihat Semua Postingan</a>
        <?php else: ?>
            <button onclick="openModal('accessRestrictedModal')" class="btn btn-outline">Lihat Semua Postingan</button>
        <?php endif; ?>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
