<?php
/**
 * Connexion à l'espace administrateur.
 */
require_once __DIR__ . '/../includes/functions.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!empty($_SESSION['admin_id'])) {
    header('Location: index.php');
    exit;
}

$erreur = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_valide($_POST['csrf_token'] ?? null)) {
        $erreur = 'Session expirée, veuillez réessayer.';
    } else {
        $utilisateur = trim($_POST['utilisateur'] ?? '');
        $motDePasse  = $_POST['mot_de_passe'] ?? '';

        $stmt = db()->prepare('SELECT * FROM admins WHERE nom_utilisateur = ?');
        $stmt->execute([$utilisateur]);
        $admin = $stmt->fetch();

        if ($admin && password_verify($motDePasse, $admin['mot_de_passe_hash'])) {
            session_regenerate_id(true);
            $_SESSION['admin_id'] = (int) $admin['id'];
            $_SESSION['admin_nom'] = $admin['nom_utilisateur'];
            header('Location: index.php');
            exit;
        }
        $erreur = 'Identifiants incorrects.';
    }
}
?>
<!DOCTYPE html>
<html lang="fr" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion admin</title>
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
</head>
<body class="min-h-screen bg-dsi-950 flex items-center justify-center px-4 relative overflow-hidden">
    <div class="absolute -top-32 -left-32 w-96 h-96 bg-dsi-600/30 rounded-full blur-3xl"></div>
    <div class="absolute -bottom-32 -right-32 w-96 h-96 bg-dsi-500/20 rounded-full blur-3xl"></div>

    <main class="relative w-full max-w-md">
        <div class="text-center mb-8">
            <img src="../assets/img/logo.png" alt="Logo" class="h-16 w-auto mx-auto mb-4">
            <h1 class="text-2xl font-extrabold text-white">Espace administrateur</h1>
            <p class="text-slate-400 text-sm mt-2">Espace de gestion du site</p>
        </div>

        <div class="bg-white rounded-2xl shadow-2xl p-8">
            <?php if ($erreur): ?>
            <div class="mb-6 bg-red-50 border border-red-200 text-red-700 rounded-xl px-4 py-3 text-sm">
                <?= e($erreur) ?>
            </div>
            <?php endif; ?>

            <form method="post" action="login.php" class="space-y-5">
                <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">

                <div>
                    <label for="utilisateur" class="block text-sm font-semibold text-slate-700 mb-2">Nom d'utilisateur</label>
                    <input type="text" id="utilisateur" name="utilisateur" required autofocus
                           class="w-full rounded-xl border border-slate-200 px-4 py-3 focus:outline-none focus:ring-2 focus:ring-dsi-500 focus:border-dsi-500 transition"
                           placeholder="admin">
                </div>
                <div>
                    <label for="mot_de_passe" class="block text-sm font-semibold text-slate-700 mb-2">Mot de passe</label>
                    <input type="password" id="mot_de_passe" name="mot_de_passe" required
                           class="w-full rounded-xl border border-slate-200 px-4 py-3 focus:outline-none focus:ring-2 focus:ring-dsi-500 focus:border-dsi-500 transition"
                           placeholder="••••••••">
                </div>

                <button type="submit"
                        class="w-full bg-dsi-600 hover:bg-dsi-500 text-white font-semibold py-3.5 rounded-xl transition shadow-lg shadow-dsi-200">
                    Se connecter
                </button>
            </form>
        </div>

        <p class="text-center mt-6">
            <a href="../index.php" class="text-slate-400 hover:text-white text-sm transition">← Retour au site</a>
        </p>
    </main>
</body>
</html>
