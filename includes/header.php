<?php
require_once __DIR__ . '/functions.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$page_active = basename($_SERVER['PHP_SELF'], '.php');
?>
<!DOCTYPE html>
<html lang="fr" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($titre_page ?? 'Accueil') ?></title>
    <meta name="description" content="<?= e($description_page ?? SITE_TAGLINE . ' — Solutions informatiques professionnelles : développement, réseaux, sécurité et support technique.') ?>">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        dsi: {
                            50: '#eff6ff', 100: '#dbeafe', 200: '#bfdbfe', 300: '#93c5fd',
                            400: '#60a5fa', 500: '#3b82f6', 600: '#2563eb',
                            700: '#1d4ed8', 800: '#1e40af', 900: '#1e3a8a', 950: '#172554',
                        },
                    },
                },
            },
        };
    </script>
    <link rel="icon" type="image/png" href="assets/img/logo.png">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="bg-slate-50 text-slate-800 antialiased">

<!-- Barre de navigation -->
<header class="fixed top-0 inset-x-0 z-50 transition-all duration-300" id="navbar">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <nav class="flex items-center justify-between h-20">
            <a href="index.php" class="flex items-center gap-3">
                <img src="assets/img/logo.png" alt="Logo" class="h-11 w-auto">
            </a>

            <!-- Liens bureau -->
            <ul class="hidden md:flex items-center gap-8 text-sm font-medium">
                <li><a href="index.php" class="nav-link <?= $page_active === 'index' ? 'nav-link-active' : '' ?>">Accueil</a></li>
                <li><a href="services.php" class="nav-link <?= $page_active === 'services' ? 'nav-link-active' : '' ?>">Services</a></li>
                <li><a href="boutique.php" class="nav-link <?= $page_active === 'boutique' ? 'nav-link-active' : '' ?>">Vente PC</a></li>
                <li><a href="index.php#apropos" class="nav-link">À propos</a></li>
                <li><a href="contact.php" class="nav-link <?= $page_active === 'contact' ? 'nav-link-active' : '' ?>">Contact</a></li>
                <li><a href="devis.php" class="nav-link <?= $page_active === 'devis' ? 'nav-link-active' : '' ?>">Devis</a></li>
                <li>
                    <a href="devis.php" class="ml-2 inline-flex items-center gap-2 bg-dsi-600 hover:bg-dsi-500 text-white px-5 py-2.5 rounded-full font-semibold transition shadow-lg shadow-dsi-600/30">
                        Devis gratuit
                    </a>
                </li>
            </ul>

            <!-- Bouton mobile -->
            <button type="button" id="btn-menu" class="md:hidden text-white p-2" aria-label="Ouvrir le menu" aria-expanded="false">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" d="M4 7h16M4 12h16M4 17h16" id="icone-menu"/>
                </svg>
            </button>
        </nav>
    </div>

    <!-- Menu mobile -->
    <div id="menu-mobile" class="hidden md:hidden bg-dsi-950/95 backdrop-blur border-t border-white/10">
        <ul class="px-6 py-4 space-y-3 text-white font-medium">
            <li><a href="index.php" class="block py-2 hover:text-dsi-300">Accueil</a></li>
            <li><a href="services.php" class="block py-2 hover:text-dsi-300">Services</a></li>
            <li><a href="boutique.php" class="block py-2 hover:text-dsi-300">Vente PC</a></li>
            <li><a href="devis.php" class="block py-2 hover:text-dsi-300">Devis</a></li>
            <li><a href="index.php#apropos" class="block py-2 hover:text-dsi-300">À propos</a></li>
            <li><a href="contact.php" class="block py-2 hover:text-dsi-300">Contact</a></li>
        </ul>
    </div>
</header>
