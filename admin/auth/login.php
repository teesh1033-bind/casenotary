<?php
require_once __DIR__ . '/../core/bootstrap.php';

Auth::guest();

$error = '';
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!CSRF::verifyRequest()) {
        $error = 'Invalid request. Please try again.';
    } else {
        $email    = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        if (empty($email) || empty($password)) {
            $error = 'Please enter both email and password.';
            setOld(['email' => $email]);
        } else {
            $result = Auth::attempt($email, $password, 'admin');

            if ($result['success']) {
                clearOld();
                redirect('pages/dashboard.php');
            }

            $error = $result['message'];
            setOld(['email' => $email]);
        }
    }
}

$company = getCompanySettings();
$pageTitle = 'Sign In';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($pageTitle) ?> — <?= e($company['company_name']) ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="<?= asset('css/app.css') ?>" rel="stylesheet">
    <style>
        :root {
            --primary: <?= e($company['primary_color']) ?>;
            --secondary: <?= e($company['secondary_color']) ?>;
            --dark-accent: <?= e($company['dark_accent']) ?>;
        }
    </style>
</head>
<body class="auth-page">
    <div class="auth-wrapper">
        <div class="auth-visual">
            <div class="auth-visual-content">
                <div class="auth-brand">
                    <div class="auth-logo">
                        <i class="bi bi-shield-check"></i>
                    </div>
                    <h1><?= e($company['company_name']) ?></h1>
                    <p>Secure admin portal for managing notary operations, clients, cases, and documents.</p>
                </div>
                <div class="auth-features">
                    <div class="auth-feature">
                        <i class="bi bi-lock-fill"></i>
                        <span>Enterprise-grade security</span>
                    </div>
                    <div class="auth-feature">
                        <i class="bi bi-graph-up-arrow"></i>
                        <span>Real-time analytics</span>
                    </div>
                    <div class="auth-feature">
                        <i class="bi bi-people-fill"></i>
                        <span>Client & case management</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="auth-form-panel">
            <div class="auth-form-container">
                <div class="auth-form-header">
                    <h2>Welcome back</h2>
                    <p>Sign in to your admin account</p>
                </div>

                <?php if ($error): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="bi bi-exclamation-circle me-2"></i><?= e($error) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <?php if ($msg = flash('success')): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="bi bi-check-circle me-2"></i><?= e($msg) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <form method="POST" action="" class="auth-form" novalidate>
                    <?= CSRF::field() ?>

                    <div class="form-floating mb-3">
                        <input type="email" class="form-control" id="email" name="email"
                               placeholder="Email" value="<?= old('email') ?>" required autofocus>
                        <label for="email"><i class="bi bi-envelope me-2"></i>Email Address</label>
                    </div>

                    <div class="form-floating mb-3 position-relative">
                        <input type="password" class="form-control" id="password" name="password"
                               placeholder="Password" required>
                        <label for="password"><i class="bi bi-lock me-2"></i>Password</label>
                        <button type="button" class="password-toggle" id="togglePassword" tabindex="-1">
                            <i class="bi bi-eye"></i>
                        </button>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="remember" name="remember">
                            <label class="form-check-label" for="remember">Remember me</label>
                        </div>
                        <a href="<?= url('auth/forgot-password.php') ?>" class="auth-link">Forgot password?</a>
                    </div>

                    <button type="submit" class="btn btn-primary btn-auth w-100">
                        <span>Sign In</span>
                        <i class="bi bi-arrow-right"></i>
                    </button>
                </form>

                <div class="auth-footer">
                    <p>Protected by secure authentication &amp; encryption</p>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="<?= asset('js/app.js') ?>"></script>
</body>
</html>
