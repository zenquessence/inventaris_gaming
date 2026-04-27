<?php
/**
 * views/layouts/header.php  (updated — sidebar layout)
 * Security: all dynamic output uses htmlspecialchars().
 */
$flash_success = $_SESSION['flash_success'] ?? null;
$flash_error   = $_SESSION['flash_error']   ?? null;
unset($_SESSION['flash_success'], $_SESSION['flash_error']);

$safeTitle = htmlspecialchars($pageTitle ?? 'Dashboard', ENT_QUOTES, 'UTF-8');
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $safeTitle ?> — Inventaris Gaming</title>
    <meta name="description" content="Sistem manajemen inventaris barang gaming.">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Orbitron:wght@700;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/inventaris/css/css/style.css">
</head>
<body>

<div class="app-layout">

    <?php require_once __DIR__ . '/sidebar.php'; ?>

    <div class="main-content">

        <!-- Top bar -->
        <header class="content-topbar">
            <div class="topbar-title">
                <h1><?= $safeTitle ?></h1>
            </div>
            <div class="topbar-right">
                <span class="topbar-date">
                    <?= htmlspecialchars(
                        strftime('%A, %d %B %Y') ?: date('l, d F Y'),
                        ENT_QUOTES, 'UTF-8'
                    ) ?>
                </span>
            </div>
        </header>

        <!-- Content body -->
        <div class="content-body">

            <?php if ($flash_success): ?>
            <div class="flash flash-success" role="alert">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                    <polyline points="20 6 9 17 4 12"/>
                </svg>
                <?= htmlspecialchars($flash_success, ENT_QUOTES, 'UTF-8') ?>
            </div>
            <?php endif; ?>

            <?php if ($flash_error): ?>
            <div class="flash flash-error" role="alert">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                    <circle cx="12" cy="12" r="10"/>
                    <line x1="15" y1="9" x2="9" y2="15"/>
                    <line x1="9" y1="9" x2="15" y2="15"/>
                </svg>
                <?= htmlspecialchars($flash_error, ENT_QUOTES, 'UTF-8') ?>
            </div>
            <?php endif; ?>
