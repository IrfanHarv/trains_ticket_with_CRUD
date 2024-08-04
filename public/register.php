<?php
require_once '../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);

    // Periksa apakah email sudah terdaftar
    $stmt = $pdo->prepare('SELECT COUNT(*) FROM users WHERE email = ?');
    $stmt->execute([$email]);
    $emailExists = $stmt->fetchColumn();

    if ($emailExists) {
        $error = 'Email sudah terdaftar. Silakan gunakan email lain atau login.';
    } else {
        // Siapkan statement insert
        $stmt = $pdo->prepare('INSERT INTO users (name, email, password) VALUES (?, ?, ?)');
        
        // Eksekusi statement dengan data pengguna yang disediakan
        if ($stmt->execute([$name, $email, $password])) {
            // Redirect ke halaman login setelah pendaftaran berhasil
            header('Location: login.php');
            exit();
        } else {
            $error = 'Gagal mendaftar. Silakan coba lagi.';
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Register | Trains Booking</title>
    <link rel="stylesheet" href="../css/bootstrap.min.css">
</head>
<body>
<?php include '../includes/navbar.php'; ?>
<div class="container">
    <h1 class="text-center mt-5">Trains Booking</h1>
    <div class="row justify-content-center mt-5">
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <h2 class="card-title text-center">Register</h2>
                    <?php if (isset($error)): ?>
                        <div class="alert alert-danger"><?= $error ?></div>
                    <?php endif; ?>
                    <form method="post">
                        <div class="mb-3">
                            <label for="name" class="form-label">Name</label>
                            <input type="text" class="form-control" id="name" name="name" required>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">Register</button>
                        </div>
                    </form>
                    <div class="text-center mt-3">
                        <p>Sudah punya akun? <a href="login.php">Login</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="../js/bootstrap.bundle.min.js"></script>
</body>
</html>