<?php
/**
 * views/auth/login.php
 * Standalone login page — does NOT use the shared header/footer layout.
 * Security: CSRF hidden field, all output htmlspecialchars'd.
 */
// Capture & clear flash messages
$flash_success = $_SESSION['flash_success'] ?? null;
$flash_error = $_SESSION['flash_error'] ?? null;
unset($_SESSION['flash_success'], $_SESSION['flash_error']);

$csrf = htmlspecialchars($_SESSION['csrf_token'] ?? '', ENT_QUOTES, 'UTF-8');
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — Gaming Cloud</title>
    <meta name="description" content="Login ke sistem Gaming Cloud.">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Orbitron:wght@700;900&display=swap"
        rel="stylesheet">
    <style>
        *,
        *::before,
        *::after {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #03010d;
            overflow: hidden;
            position: relative;
        }

        /* Animated background */
        body::before {
            content: '';
            position: fixed;
            inset: 0;
            background:
                radial-gradient(ellipse 80% 60% at 20% 30%, rgba(124, 58, 237, .18) 0%, transparent 60%),
                radial-gradient(ellipse 60% 50% at 80% 70%, rgba(59, 130, 246, .12) 0%, transparent 55%),
                radial-gradient(ellipse 40% 40% at 50% 10%, rgba(167, 139, 250, .08) 0%, transparent 50%);
            z-index: 0;
        }

        /* Grid overlay */
        body::after {
            content: '';
            position: fixed;
            inset: 0;
            background-image:
                linear-gradient(rgba(124, 58, 237, .04) 1px, transparent 1px),
                linear-gradient(90deg, rgba(124, 58, 237, .04) 1px, transparent 1px);
            background-size: 40px 40px;
            z-index: 0;
        }

        .login-wrapper {
            position: relative;
            z-index: 1;
            width: 100%;
            max-width: 420px;
            padding: 1.5rem;
            animation: fadeUp .5s ease;
        }

        @keyframes fadeUp {
            from {
                opacity: 0;
                transform: translateY(24px);
            }

            to {
                opacity: 1;
                transform: none;
            }
        }

        /* Brand logo above card */
        .login-brand {
            text-align: center;
            margin-bottom: 1.75rem;
        }

        .login-brand .icon {
            font-size: 2.8rem;
            display: block;
            margin-bottom: .5rem;
            filter: drop-shadow(0 0 20px rgba(124, 58, 237, .7));
            animation: pulse 3s ease-in-out infinite;
        }

        @keyframes pulse {

            0%,
            100% {
                filter: drop-shadow(0 0 16px rgba(124, 58, 237, .6));
            }

            50% {
                filter: drop-shadow(0 0 32px rgba(167, 139, 250, .9));
            }
        }

        .login-brand h1 {
            font-family: 'Orbitron', sans-serif;
            font-size: 1.1rem;
            font-weight: 900;
            letter-spacing: .08em;
            background: linear-gradient(135deg, #a78bfa, #60a5fa);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .login-brand p {
            font-size: .78rem;
            color: #64748b;
            margin-top: .3rem;
            letter-spacing: .04em;
        }

        /* Card */
        .login-card {
            background: rgba(13, 18, 32, .85);
            border: 1px solid rgba(124, 58, 237, .3);
            border-radius: 16px;
            padding: 2rem;
            backdrop-filter: blur(20px);
            box-shadow:
                0 0 0 1px rgba(124, 58, 237, .1),
                0 24px 60px rgba(0, 0, 0, .6),
                inset 0 1px 0 rgba(255, 255, 255, .06);
            position: relative;
            overflow: hidden;
        }

        /* Top-edge glow */
        .login-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 10%;
            right: 10%;
            height: 1px;
            background: linear-gradient(90deg, transparent, rgba(167, 139, 250, .7), transparent);
        }

        .card-title {
            font-size: 1.15rem;
            font-weight: 700;
            color: #e2e8f0;
            margin-bottom: .35rem;
        }

        .card-sub {
            font-size: .8rem;
            color: #64748b;
            margin-bottom: 1.6rem;
        }

        /* Flash */
        .flash {
            display: flex;
            align-items: center;
            gap: .6rem;
            padding: .75rem 1rem;
            border-radius: 8px;
            font-size: .82rem;
            font-weight: 500;
            margin-bottom: 1.2rem;
        }

        .flash-success {
            background: rgba(34, 197, 94, .1);
            border: 1px solid rgba(34, 197, 94, .3);
            color: #4ade80;
        }

        .flash-error {
            background: rgba(239, 68, 68, .1);
            border: 1px solid rgba(239, 68, 68, .3);
            color: #f87171;
        }

        /* Form */
        .form-group {
            margin-bottom: 1.1rem;
        }

        .form-label {
            display: block;
            font-size: .78rem;
            font-weight: 600;
            color: #94a3b8;
            margin-bottom: .4rem;
            letter-spacing: .03em;
        }

        .input-wrap {
            position: relative;
        }

        .input-icon {
            position: absolute;
            left: .85rem;
            top: 50%;
            transform: translateY(-50%);
            color: #475569;
            pointer-events: none;
            width: 16px;
            height: 16px;
        }

        .form-control {
            width: 100%;
            padding: .7rem .9rem .7rem 2.4rem;
            background: rgba(255, 255, 255, .05);
            border: 1px solid rgba(99, 102, 241, .2);
            border-radius: 9px;
            color: #e2e8f0;
            font-size: .875rem;
            font-family: 'Inter', sans-serif;
            transition: border-color .2s, box-shadow .2s;
        }

        .form-control:focus {
            outline: none;
            border-color: #7c3aed;
            box-shadow: 0 0 0 3px rgba(124, 58, 237, .2);
            background: rgba(124, 58, 237, .05);
        }

        .form-control::placeholder {
            color: #475569;
        }

        /* Submit button */
        .btn-login {
            width: 100%;
            padding: .8rem;
            background: linear-gradient(135deg, #7c3aed, #4f46e5);
            color: #fff;
            font-size: .9rem;
            font-weight: 700;
            border: none;
            border-radius: 9px;
            cursor: pointer;
            font-family: 'Inter', sans-serif;
            letter-spacing: .04em;
            box-shadow: 0 0 24px rgba(124, 58, 237, .4);
            transition: all .25s;
            margin-top: .6rem;
            position: relative;
            overflow: hidden;
        }

        .btn-login::after {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, #8b5cf6, #6366f1);
            opacity: 0;
            transition: opacity .25s;
        }

        .btn-login:hover {
            box-shadow: 0 0 36px rgba(124, 58, 237, .65);
            transform: translateY(-1px);
        }

        .btn-login:hover::after {
            opacity: 1;
        }

        .btn-login span {
            position: relative;
            z-index: 1;
        }

        .btn-login:active {
            transform: none;
        }

        /* Footer note */
        .login-footer {
            text-align: center;
            margin-top: 1.25rem;
            font-size: .72rem;
            color: #334155;
        }
    </style>
</head>

<body>

    <div class="login-wrapper">

        <div class="login-brand">
            <span class="icon">🎮</span>
            <h1>GAMING CLOUD</h1>
            <p>SISTEM MANAJEMEN CLOUD</p>
        </div>

        <div class="login-card">
            <h2 class="card-title">Selamat Datang</h2>
            <p class="card-sub">Masuk ke akun Anda untuk melanjutkan</p>

            <?php if ($flash_success): ?>
                <div class="flash flash-success" role="alert">
                    ✓ <?= htmlspecialchars($flash_success, ENT_QUOTES, 'UTF-8') ?>
                </div>
            <?php endif; ?>

            <?php if ($flash_error): ?>
                <div class="flash flash-error" role="alert">
                    ✗ <?= htmlspecialchars($flash_error, ENT_QUOTES, 'UTF-8') ?>
                </div>
            <?php endif; ?>

            <form action="index.php?page=login&action=proses" method="POST" novalidate>
                <input type="hidden" name="csrf_token" value="<?= $csrf ?>">

                <div class="form-group">
                    <label class="form-label" for="username">USERNAME</label>
                    <div class="input-wrap">
                        <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" />
                            <circle cx="12" cy="7" r="4" />
                        </svg>
                        <input type="text" id="username" name="username" class="form-control"
                            placeholder="Masukkan username"
                            value="<?= htmlspecialchars($_POST['username'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                            autocomplete="username" required maxlength="50">
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label" for="password">PASSWORD</label>
                    <div class="input-wrap">
                        <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <rect x="3" y="11" width="18" height="11" rx="2" ry="2" />
                            <path d="M7 11V7a5 5 0 0 1 10 0v4" />
                        </svg>
                        <input type="password" id="password" name="password" class="form-control"
                            placeholder="Masukkan password" autocomplete="current-password" required>
                    </div>
                </div>

                <button type="submit" class="btn-login" id="btn-submit">
                    <span>MASUK →</span>
                </button>
            </form>
        </div>

        <p class="login-footer">
            &copy; <?= date('Y') ?> Gaming Cloud &bull; Secure MVC PHP
        </p>
    </div>

</body>

</html>