<?php
/**
 * En-tête commun de l'espace admin (à inclure après garde.php).
 * $titre_admin doit être défini avant l'inclusion.
 */
$non_lus = compter_messages_non_lus();
$menu_admin = [
    'index'         => ['Tableau de bord', 'dashboard'],
    'services'      => ['Services', 'grid'],
    'ordinateurs'   => ['Vente PC', 'laptop'],
    'devis'         => ['Devis' . (compter_demandes_devis_non_traitees() > 0 ? ' (' . compter_demandes_devis_non_traitees() . ')' : ''), 'clipboard'],
    'logo'         => ['Logo du site', 'image'],
    'messages'      => ['Messages' . ($non_lus > 0 ? ' (' . $non_lus . ')' : ''), 'mail'],
];
$page_courante = basename($_SERVER['PHP_SELF'], '.php');
?>
<!DOCTYPE html>
<html lang="fr" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($titre_admin) ?> — Admin</title>
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
    <link rel="icon" type="image/png" href="../assets/img/logo.png">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body class="bg-slate-100 text-slate-800 antialiased">

<div class="flex min-h-screen">

    <!-- Barre latérale -->
    <aside class="bg-dsi-950 text-slate-300 w-64 shrink-0 hidden lg:flex flex-col">
        <div class="flex items-center gap-3 px-6 h-20 border-b border-white/10">
            <img src="../assets/img/logo.png" alt="Logo" class="h-10 w-auto">
            <p class="text-xs text-slate-500">Administration</p>
        </div>
        <nav class="flex-1 px-4 py-6 space-y-1">
            <?php foreach ($menu_admin as $page => [$libelle, $icone]): ?>
            <a href="<?= $page ?>.php"
               class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium transition
                      <?= $page_courante === $page ? 'bg-dsi-600 text-white shadow-lg shadow-dsi-600/30' : 'hover:bg-white/5 hover:text-white' ?>">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                    <?php
                    $icones = [
                        'dashboard' => '<path stroke-linecap="round" stroke-linejoin="round" d="M4 4h7v7H4zM13 4h7v4h-7zM13 10h7v10h-7zM4 13h7v7H4z"/>',
                        'grid'      => '<path stroke-linecap="round" stroke-linejoin="round" d="M4 5h6v6H4zM14 5h6v6h-6zM4 15h6v6H4zM14 15h6v6h-6z"/>',
                        'laptop'    => '<rect x="4" y="5" width="16" height="11" rx="2"/><path d="M2 19h20"/>',
                        'clipboard' => '<rect x="6" y="4" width="12" height="17" rx="2"/><path d="M9 4a3 3 0 0 1 6 0M9 11h6M9 15h4"/>',
                        'image'     => '<rect x="3" y="5" width="18" height="14" rx="2"/><circle cx="9" cy="10" r="1.5"/><path d="M21 15l-5-5-9 9"/>',
                        'mail'      => '<path stroke-linecap="round" stroke-linejoin="round" d="M3 8l9 6 9-6M3 6h18v12H3V6z"/>',
                    ];
                    echo $icones[$icone];
                    ?>
                </svg>
                <?= e($libelle) ?>
            </a>
            <?php endforeach; ?>
        </nav>
        <div class="px-4 py-6 border-t border-white/10">
            <a href="../index.php" class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm hover:bg-white/5 hover:text-white transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 16l-4-4 4-4m-4 4h14m-5 4v1a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V7a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v1"/></svg>
                Voir le site
            </a>
            <a href="logout.php" class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm text-red-300 hover:bg-red-500/10 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4-4-4m4 4H7m4 4v1a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V7a2 2 0 0 1 2-2h3a2 2 0 0 1 2 2v1"/></svg>
                Déconnexion
            </a>
        </div>
    </aside>

    <!-- Zone principale -->
    <div class="flex-1 flex flex-col min-w-0">
        <!-- Barre supérieure mobile -->
        <div class="lg:hidden bg-dsi-950 text-white px-4 h-16 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <img src="../assets/img/logo.png" alt="Logo" class="h-8 w-auto">
                <span class="font-bold">Admin</span>
            </div>
            <div class="flex gap-3 text-sm">
                <a href="services.php" class="px-3 py-1.5 rounded-lg hover:bg-white/10">Services</a>
                <a href="ordinateurs.php" class="px-3 py-1.5 rounded-lg hover:bg-white/10">PC</a>
                <a href="devis.php" class="px-3 py-1.5 rounded-lg hover:bg-white/10">Devis</a>
                <a href="messages.php" class="px-3 py-1.5 rounded-lg hover:bg-white/10">Messages<?= $non_lus > 0 ? ' (' . $non_lus . ')' : '' ?></a>
                <a href="logout.php" class="px-3 py-1.5 rounded-lg text-red-300 hover:bg-red-500/10">Quitter</a>
            </div>
        </div>

        <header class="bg-white border-b border-slate-200 px-6 lg:px-10 h-20 flex items-center justify-between">
            <h1 class="text-xl lg:text-2xl font-extrabold text-slate-900"><?= e($titre_admin) ?></h1>
            <p class="text-sm text-slate-400 hidden sm:block">
                Connecté en tant que <span class="font-semibold text-slate-600"><?= e($_SESSION['admin_nom'] ?? '') ?></span>
            </p>
        </header>

        <main class="flex-1 p-6 lg:p-10">
