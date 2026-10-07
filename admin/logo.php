<?php
/**
 * Téléversement / remplacement du logo du site (et du proforma).
 */
require_once __DIR__ . '/garde.php';
$titre_admin = 'Logo du site';

$notification = '';
$type_notification = 'succes';
$chemin_logo = __DIR__ . '/../assets/img/logo.png';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && csrf_valide($_POST['csrf_token'] ?? null)) {
    if ($_POST['action'] === 'uploader' && isset($_FILES['logo']) && $_FILES['logo']['error'] === UPLOAD_ERR_OK) {
        $fichier = $_FILES['logo'];
        $infos = @getimagesize($fichier['tmp_name']);
        $extension = strtolower(pathinfo($fichier['name'], PATHINFO_EXTENSION));

        if ($fichier['size'] > 2 * 1024 * 1024) {
            $notification = 'Fichier trop lourd (2 Mo maximum).';
            $type_notification = 'erreur';
        } elseif ($infos === false) {
            $notification = 'Ce fichier n\'est pas une image valide.';
            $type_notification = 'erreur';
        } elseif (!in_array($infos[2], [IMAGETYPE_PNG], true)) {
            $notification = 'Format non supporté : utilisez une image PNG (idéalement avec fond transparent).';
            $type_notification = 'erreur';
        } else {
            // Sauvegarde de l'ancien logo, puis remplacement
            if (!file_exists(__DIR__ . '/../assets/img/logo-backup.png')) {
                copy($chemin_logo, __DIR__ . '/../assets/img/logo-backup.png');
            }
            if (move_uploaded_file($fichier['tmp_name'], $chemin_logo)) {
                $notification = 'Logo remplacé avec succès ! Il est visible immédiatement sur tout le site et les devis.';
            } else {
                $notification = 'Erreur lors de l\'enregistrement du fichier.';
                $type_notification = 'erreur';
            }
        }
    } elseif ($_POST['action'] === 'restaurer' && file_exists(__DIR__ . '/../assets/img/logo-backup.png')) {
        copy(__DIR__ . '/../assets/img/logo-backup.png', $chemin_logo);
        $notification = 'Ancien logo restauré.';
    }
}

require __DIR__ . '/entete.php';
?>

<?php if ($notification): ?>
<div class="mb-8 rounded-xl px-5 py-4 border text-sm font-medium
     <?= $type_notification === 'erreur' ? 'bg-red-50 border-red-200 text-red-700' : 'bg-emerald-50 border-emerald-200 text-emerald-700' ?>">
    <?= e($notification) ?>
</div>
<?php endif; ?>

<div class="grid lg:grid-cols-2 gap-8 items-start max-w-4xl">

    <!-- Aperçu actuel -->
    <section class="bg-white rounded-2xl shadow-sm border border-slate-100 p-8">
        <h2 class="font-bold text-slate-900 mb-6">Logo actuel</h2>
        <div class="bg-gradient-to-br from-dsi-950 to-dsi-800 rounded-2xl p-10 flex items-center justify-center">
            <img src="../assets/img/logo.png?v=<?= filemtime($chemin_logo) ?>" alt="Logo actuel" class="max-h-40 w-auto">
        </div>
        <p class="text-xs text-slate-400 mt-4">
            Ce logo apparaît dans la navigation, le pied de page, la page de connexion,
            l'espace admin et les devis proforma.
        </p>
        <?php if (file_exists(__DIR__ . '/../assets/img/logo-backup.png')): ?>
        <form method="post" action="logo.php" class="mt-4">
            <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
            <input type="hidden" name="action" value="restaurer">
            <button type="submit"
                    class="text-sm font-semibold px-5 py-2.5 rounded-xl border border-slate-200 text-slate-500 hover:bg-slate-50 transition">
                ↩ Restaurer le logo précédent
            </button>
        </form>
        <?php endif; ?>
    </section>

    <!-- Upload -->
    <section class="bg-white rounded-2xl shadow-sm border border-slate-100 p-8">
        <h2 class="font-bold text-slate-900 mb-6">Changer le logo</h2>
        <form method="post" action="logo.php" enctype="multipart/form-data" class="space-y-6">
            <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
            <input type="hidden" name="action" value="uploader">

            <label for="logo" id="zone-depot"
                   class="flex flex-col items-center justify-center gap-3 border-2 border-dashed border-dsi-300 bg-dsi-50/50 rounded-2xl py-12 cursor-pointer hover:bg-dsi-50 transition">
                <svg class="w-12 h-12 text-dsi-400" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5V18a3 3 0 0 0 3 3h12a3 3 0 0 0 3-3v-1.5M12 3v13.5m0-13.5L7.5 7.5M12 3l4.5 4.5"/></svg>
                <p class="font-semibold text-dsi-700">Cliquez pour choisir un fichier</p>
                <p class="text-xs text-slate-400" id="nom-fichier">PNG uniquement · 2 Mo max · fond transparent conseillé</p>
                <input type="file" id="logo" name="logo" accept="image/png" required class="hidden">
            </label>

            <button type="submit"
                    class="w-full bg-dsi-600 hover:bg-dsi-500 text-white font-semibold py-3.5 rounded-xl transition shadow-lg shadow-dsi-200">
                Téléverser le nouveau logo
            </button>
        </form>
    </section>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const input = document.getElementById('logo');
    const nom = document.getElementById('nom-fichier');
    if (input) {
        input.addEventListener('change', () => {
            if (input.files.length > 0) {
                nom.textContent = input.files[0].name + ' (' + Math.round(input.files[0].size / 1024) + ' Ko)';
            }
        });
    }
});
</script>

<?php require __DIR__ . '/pied.php'; ?>
