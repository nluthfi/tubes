<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Street Food Gegerkalong</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="login.css">
</head>
<body>

    <div class="login-container d-flex align-items-center justify-content-center">
        <div class="login-box card p-4 p-sm-5 text-center">
            
            <div class="login-logo mb-3">
                🍜 STREET FOOD <span>GEGERKALONG</span>
            </div>
            
            <h2 class="h5 text-white fw-bold mb-1">Selamat Datang</h2>
            <p class="subtitle mb-4">Silakan masuk ke akun Anda</p>

            <form  action="proses_login.php" method="POST">
                
                <div class="mb-3 text-start">
                    <label for="username" class="form-label text-light-gray small fw-medium mb-2">Username atau Email</label>
                    <div class="input-group-kustom">
                        <i class="bi bi-person-fill input-icon"></i>
                        <input type="text" class="form-control-kustom" id="username" name="username" placeholder="Masukkan username Anda..." required>
                    </div>
                </div>

                <div class="mb-4 text-start">
                    <label for="password" class="form-label text-light-gray small fw-medium mb-2">Password</label>
                    <div class="input-group-kustom">
                        <i class="bi bi-lock-fill input-icon"></i>
                        <input type="password" class="form-control-kustom" id="password" name="password" placeholder="Masukkan kata sandi..." required>
                    </div>
                </div>
                <button type="submit" class="btn-accent-kustom w-100 fw-bold py-3 mb-2">Masuk</button>
            </form>

            <div class="mt-3">
                <a href="..\index.php" class="text-secondary-gray text-decoration-none">
                    <i class="bi bi-arrow-left"></i>
                    Kembali ke Beranda
                </a>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="script.js"></script>
</body>
</html>