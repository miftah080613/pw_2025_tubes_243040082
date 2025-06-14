<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Blog Perjalanan</title>
    <link rel="stylesheet" href="/travel_blog/assets/style.css">
</head>
<body>
<header class="nav-momo">
    <div class="nav-momo-inner">
        <a href="/travel_blog/index.php" class="nav-momo-logo">
            <img src="https://source.unsplash.com/80x80/?travel,logo" alt="Logo" style="height:48px;width:48px;border-radius:50%;object-fit:cover;">
        </a>
        <nav class="nav-momo-menu">
            <a href="/travel_blog/index.php">Beranda</a>
            <?php if (isset($_SESSION['user_id'])): ?>
                <a href="/travel_blog/posts/list.php">Postingan</a>
                <a href="/travel_blog/wishlist/list.php">Wishlist Saya</a>
                <a href="/travel_blog/users/logout.php" class="logout-btn">Logout</a>
            <?php else: ?>
                <button onclick="openModal('loginModal')" class="nav-btn">Login</button>
                <button onclick="openModal('registerModal')" class="nav-btn">Daftar</button>
            <?php endif; ?>
        </nav>
    </div>
</header>

<!-- Modal Login -->
<div id="loginModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal('loginModal')">&times;</span>
        <h2>Login</h2>
        <form id="loginForm" action="/travel_blog/users/login.php" method="post">
            <div class="form-group">
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" required>
            </div>
            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit" class="submit-btn">Login</button>
        </form>
        <div id="loginMessage" class="modal-message" style="display:none;"></div>
        <p style="text-align:center;margin-top:15px;">Belum punya akun? <a href="#" onclick="closeModal('loginModal'); openModal('registerModal')" style="color:#007bff;text-decoration:none;font-weight:bold;">Daftar sekarang</a></p>
    </div>
</div>

<!-- Modal Register -->
<div id="registerModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal('registerModal')">&times;</span>
        <h2>Daftar</h2>
        <form id="registerForm" action="/travel_blog/users/register.php" method="post">
            <div class="form-group">
                <label for="reg-username">Username:</label>
                <input type="text" id="reg-username" name="username" required>
            </div>
            <div class="form-group">
                <label for="reg-password">Password:</label>
                <input type="password" id="reg-password" name="password" required>
            </div>
            <button type="submit" class="submit-btn">Daftar</button>
        </form>
        <p style="text-align:center;margin-top:15px;">Sudah punya akun? <a href="#" onclick="closeModal('registerModal'); openModal('loginModal')" style="color:#007bff;text-decoration:none;font-weight:bold;">Login di sini</a></p>
        <div id="registerMessage" class="modal-message" style="display:none;"></div>
    </div>
</div>

<!-- Modal Buat Postingan Baru -->
<div id="postModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal('postModal')">&times;</span>
        <h2>Buat Postingan Baru</h2>
        <form id="postForm" method="post" enctype="multipart/form-data">
            <label for="title">Judul:</label>
            <input type="text" id="title" name="title" required>
            <label for="content">Konten:</label>
            <textarea id="content" name="content" rows="5" required></textarea>
            <label for="image">Upload Gambar:</label>
            <input type="file" id="image" name="image" accept="image/*">
            <button type="submit">Posting</button>
        </form>
    </div>
</div>

<script>
function openModal(modalId) {
    document.getElementById(modalId).style.display = "block";
    // Bersihkan pesan error/sukses sebelumnya ketika modal dibuka
    if (modalId === 'loginModal') {
        document.getElementById('loginMessage').style.display = 'none';
        document.getElementById('loginMessage').textContent = '';
    } else if (modalId === 'registerModal') {
        document.getElementById('registerMessage').style.display = 'none';
        document.getElementById('registerMessage').textContent = '';
    }
}

function closeModal(modalId) {
    document.getElementById(modalId).style.display = "none";
}

// Tutup modal ketika user klik di luar modal
window.onclick = function(event) {
    if (event.target.className === 'modal') {
        event.target.style.display = "none";
    }
}

// Handle form pendaftaran (Register)
document.getElementById('registerForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    const form = e.target;
    const formData = new FormData(form);
    const registerMessageDiv = document.getElementById('registerMessage');

    try {
        const response = await fetch(form.action, {
            method: 'POST',
            body: formData
        });
        const result = await response.json();

        registerMessageDiv.style.display = 'block';
        if (result.success) {
            registerMessageDiv.className = 'modal-message success-message';
            registerMessageDiv.textContent = result.message;
            // Opsional: kosongkan form atau arahkan ke login setelah sukses
            form.reset();
            setTimeout(() => {
                closeModal('registerModal');
                openModal('loginModal');
            }, 1500); // Tunggu 1.5 detik lalu buka modal login
        } else {
            registerMessageDiv.className = 'modal-message error-message';
            registerMessageDiv.textContent = result.message;
        }
    } catch (error) {
        registerMessageDiv.style.display = 'block';
        registerMessageDiv.className = 'modal-message error-message';
        registerMessageDiv.textContent = 'Terjadi kesalahan jaringan atau server.';
        console.error('Error:', error);
    }
});

// Handle form login
document.getElementById('loginForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    const form = e.target;
    const formData = new FormData(form);
    const loginMessageDiv = document.getElementById('loginMessage');

    try {
        const response = await fetch(form.action, {
            method: 'POST',
            body: formData
        });
        const result = await response.json();

        loginMessageDiv.style.display = 'block';
        if (result.success) {
            loginMessageDiv.className = 'modal-message success-message';
            loginMessageDiv.textContent = result.message;
            // Refresh halaman setelah login sukses
            setTimeout(() => {
                window.location.reload();
            }, 1000); // Tunggu 1 detik lalu refresh
        } else {
            loginMessageDiv.className = 'modal-message error-message';
            loginMessageDiv.textContent = result.message;
        }
    } catch (error) {
        loginMessageDiv.style.display = 'block';
        loginMessageDiv.className = 'modal-message error-message';
        loginMessageDiv.textContent = 'Terjadi kesalahan jaringan atau server.';
        console.error('Error:', error);
    }
});
</script>

<!-- Modal Akses Terbatas (untuk Posts) -->
<div id="accessRestrictedModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal('accessRestrictedModal')">&times;</span>
        <h2>Akses Dibatasi</h2>
        <p style="font-size:1.1em;color:#555;text-align:center;margin-bottom:25px;">Anda wajib login atau mendaftar untuk melihat semua postingan.</p>
        <div style="display:flex;justify-content:center;gap:15px;">
            <button onclick="closeModal('accessRestrictedModal'); openModal('loginModal')" class="btn btn-primary">Login</button>
            <button onclick="closeModal('accessRestrictedModal'); openModal('registerModal')" class="btn btn-secondary">Daftar</button>
        </div>
    </div>
</div>

<main>
