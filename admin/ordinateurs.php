<?php
/**
 * Gestion des ordinateurs en vente.
 */
require_once __DIR__ . '/garde.php';
$titre_admin = 'Vente d\'ordinateurs';

$notification = '';
$type_notification = 'succes';

$images_disponibles = [];
foreach (glob(__DIR__ . '/../assets/img/pc/*.jpg') as $fichier) {
    $chemin = 'assets/img/pc/' . basename($fichier);
    $images_disponibles[$chemin] = basename($fichier);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_valide($_POST['csrf_token'] ?? null)) {
        $notification = 'Session expirée, veuillez réessayer.';
        $type_notification = 'erreur';
    } else {
        $action = $_POST['action'] ?? '';

        if ($action === 'creer' || $action === 'modifier') {
            $id          = (int) ($_POST['id'] ?? 0);
            $nom         = trim($_POST['nom'] ?? '');
            $description = trim($_POST['description'] ?? '');
            $prix        = trim($_POST['prix'] ?? '');
            $devise      = trim($_POST['devise'] ?? 'FCFA') ?: 'FCFA';
            $image       = in_array($_POST['image'] ?? '', array_keys($images_disponibles), true) ? $_POST['image'] : 'assets/img/pc/pc-1.jpg';
            $ordre       = max(0, (int) ($_POST['ordre_affichage'] ?? 0));
            $actif       = isset($_POST['actif']) ? 1 : 0;
            $prix        = ($prix !== '' && is_numeric($prix)) ? $prix : null;

            if ($nom === '' || mb_strlen($nom) > 150) {
                $notification = 'Le nom est obligatoire (150 caractères max).';
                $type_notification = 'erreur';
            } elseif ($description === '') {
                $notification = 'La description est obligatoire.';
                $type_notification = 'erreur';
            } else {
                if ($action === 'creer') {
                    db()->prepare('INSERT INTO ordinateurs (nom, description, prix, devise, image, actif, ordre_affichage) VALUES (?, ?, ?, ?, ?, ?, ?)')
                        ->execute([$nom, $description, $prix, $devise, $image, $actif, $ordre]);
                    $notification = 'Ordinateur ajouté avec succès.';
                } else {
                    db()->prepare('UPDATE ordinateurs SET nom = ?, description = ?, prix = ?, devise = ?, image = ?, actif = ?, ordre_affichage = ? WHERE id = ?')
                        ->execute([$nom, $description, $prix, $devise, $image, $actif, $ordre, $id]);
                    $notification = 'Ordinateur modifié avec succès.';
                }
            }
        } elseif ($action === 'supprimer') {
            db()->prepare('DELETE FROM ordinateurs WHERE id = ?')->execute([(int) ($_POST['id'] ?? 0)]);
            $notification = 'Ordinateur supprimé.';
        } elseif ($action === 'basculer') {
            db()->prepare('UPDATE ordinateurs SET actif = 1 - actif WHERE id = ?')->execute([(int) ($_POST['id'] ?? 0)]);
            $notification = 'Statut mis à jour.';
        }
    }
}

$ordinateurs = get_ordinateurs(true);
$en_edition  = null;
if (isset($_GET['modifier'])) {
    $en_edition = get_ordinateur((int) $_GET['modifier']);
}

require __DIR__ . '/entete.php';
?>

<?php if ($notification): ?>
<div class="mb-8 rounded-xl px-5 py-4 border text-sm font-medium
     <?= $type_notification === 'erreur' ? 'bg-red-50 border-red-200 text-red-700' : 'bg-emerald-50 border-emerald-200 text-emerald-700' ?>">
    <?= e($notification) ?>
</div>
<?php endif; ?>

<div class="grid lg:grid-cols-3 gap-8 items-start">

    <!-- Formulaire -->
    <section class="lg:col-span-1 bg-white rounded-2xl shadow-sm border border-slate-100 p-7">
        <h2 class="font-bold text-slate-900 mb-6"><?= $en_edition ? 'Modifier l\'ordinateur' : 'Ajouter un ordinateur' ?></h2>

        <form method="post" action="ordinateurs.php" class="space-y-5">
            <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
            <input type="hidden" name="action" value="<?= $en_edition ? 'modifier' : 'creer' ?>">
            <input type="hidden" name="id" value="<?= $en_edition ? (int) $en_edition['id'] : '' ?>">

            <div>
                <label for="nom" class="block text-sm font-semibold text-slate-700 mb-2">Nom *</label>
                <input type="text" id="nom" name="nom" required maxlength="150"
                       value="<?= e($en_edition['nom'] ?? '') ?>"
                       class="w-full rounded-xl border border-slate-200 px-4 py-3 focus:outline-none focus:ring-2 focus:ring-dsi-500 transition"
                       placeholder="Ex : HP EliteBook 840 G5">
            </div>

            <div>
                <label for="description" class="block text-sm font-semibold text-slate-700 mb-2">Caractéristiques *</label>
                <textarea id="description" name="description" required rows="4"
                          class="w-full rounded-xl border border-slate-200 px-4 py-3 focus:outline-none focus:ring-2 focus:ring-dsi-500 transition resize-y"
                          placeholder="Ex : Intel Core i5, 8 Go RAM, SSD 256 Go…"><?= e($en_edition['description'] ?? '') ?></textarea>
            </div>

            <div class="grid grid-cols-3 gap-4">
                <div class="col-span-2">
                    <label for="prix" class="block text-sm font-semibold text-slate-700 mb-2">Prix</label>
                    <input type="number" id="prix" name="prix" min="0" step="0.01"
                           value="<?= e($en_edition['prix'] ?? '') ?>"
                           class="w-full rounded-xl border border-slate-200 px-4 py-3 focus:outline-none focus:ring-2 focus:ring-dsi-500 transition"
                           placeholder="Vide = à consulter">
                </div>
                <div>
                    <label for="devise" class="block text-sm font-semibold text-slate-700 mb-2">Devise</label>
                    <input type="text" id="devise" name="devise" maxlength="10"
                           value="<?= e($en_edition['devise'] ?? 'FCFA') ?>"
                           class="w-full rounded-xl border border-slate-200 px-4 py-3 focus:outline-none focus:ring-2 focus:ring-dsi-500 transition">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label for="image" class="block text-sm font-semibold text-slate-700 mb-2">Photo</label>
                    <select id="image" name="image"
                            class="w-full rounded-xl border border-slate-200 px-4 py-3 focus:outline-none focus:ring-2 focus:ring-dsi-500 transition bg-white">
                        <?php foreach ($images_disponibles as $chemin => $libelle): ?>
                        <option value="<?= e($chemin) ?>" <?= ($en_edition['image'] ?? '') === $chemin ? 'selected' : '' ?>>
                            <?= e($libelle) ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label for="ordre" class="block text-sm font-semibold text-slate-700 mb-2">Ordre</label>
                    <input type="number" id="ordre" name="ordre_affichage" min="0"
                           value="<?= (int) ($en_edition['ordre_affichage'] ?? 0) ?>"
                           class="w-full rounded-xl border border-slate-200 px-4 py-3 focus:outline-none focus:ring-2 focus:ring-dsi-500 transition">
                </div>
            </div>

            <label class="flex items-center gap-3 cursor-pointer">
                <input type="checkbox" name="actif" <?= ($en_edition['actif'] ?? 1) ? 'checked' : '' ?>
                       class="w-5 h-5 rounded border-slate-300 text-dsi-600 focus:ring-dsi-500">
                <span class="text-sm font-semibold text-slate-700">En vente (visible sur le site)</span>
            </label>

            <div class="flex gap-3">
                <button type="submit"
                        class="flex-1 bg-dsi-600 hover:bg-dsi-500 text-white font-semibold py-3.5 rounded-xl transition shadow-lg shadow-dsi-200">
                    <?= $en_edition ? 'Enregistrer' : 'Ajouter' ?>
                </button>
                <?php if ($en_edition): ?>
                <a href="ordinateurs.php" class="px-5 py-3.5 rounded-xl border border-slate-200 text-slate-500 hover:bg-slate-50 font-semibold transition">Annuler</a>
                <?php endif; ?>
            </div>
        </form>
    </section>

    <!-- Liste -->
    <section class="lg:col-span-2 bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
        <header class="px-6 py-4 border-b border-slate-100">
            <h2 class="font-bold text-slate-900">Ordinateurs (<?= count($ordinateurs) ?>)</h2>
        </header>
        <ul class="divide-y divide-slate-100">
            <?php foreach ($ordinateurs as $pc): ?>
            <li class="px-6 py-5 flex flex-col sm:flex-row sm:items-center gap-4">
                <img src="../<?= e($pc['image']) ?>" alt="" class="w-24 h-16 rounded-lg object-cover shrink-0">
                <div class="min-w-0 flex-1">
                    <div class="flex items-center gap-2 flex-wrap">
                        <p class="font-bold text-slate-800"><?= e($pc['nom']) ?></p>
                        <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold
                            <?= $pc['actif'] ? 'bg-emerald-50 text-emerald-700' : 'bg-slate-100 text-slate-500' ?>">
                            <?= $pc['actif'] ? 'En vente' : 'Masqué' ?>
                        </span>
                    </div>
                    <p class="text-sm text-slate-400 mt-1 line-clamp-2"><?= e($pc['description']) ?></p>
                    <p class="text-sm font-bold text-dsi-700 mt-1"><?= formater_prix($pc['prix'], $pc['devise']) ?></p>
                </div>
                <div class="flex items-center gap-2 shrink-0">
                    <form method="post" action="ordinateurs.php">
                        <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                        <input type="hidden" name="action" value="basculer">
                        <input type="hidden" name="id" value="<?= (int) $pc['id'] ?>">
                        <button type="submit" title="<?= $pc['actif'] ? 'Masquer' : 'Afficher' ?>"
                                class="w-10 h-10 rounded-xl flex items-center justify-center transition
                                <?= $pc['actif'] ? 'bg-amber-50 text-amber-600 hover:bg-amber-100' : 'bg-emerald-50 text-emerald-600 hover:bg-emerald-100' ?>">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <?php if ($pc['actif']): ?>
                                <path stroke-linecap="round" d="M10.5 21h-3a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h3m4 0h3a2 2 0 0 1 2 2v3M9 12h12m-3-3l3 3-3 3" stroke-linejoin="round"/>
                                <?php else: ?>
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                <?php endif; ?>
                            </svg>
                        </button>
                    </form>
                    <a href="ordinateurs.php?modifier=<?= (int) $pc['id'] ?>" title="Modifier"
                       class="w-10 h-10 rounded-xl bg-dsi-50 text-dsi-600 hover:bg-dsi-100 flex items-center justify-center transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5h2m-1 14h8m-8-4l4-8 4 8M3 13l4-8 2.5 5"/></svg>
                    </a>
                    <form method="post" action="ordinateurs.php" class="confirmation-suppression"
                          data-message="Supprimer « <?= e($pc['nom']) ?> » ?">
                        <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                        <input type="hidden" name="action" value="supprimer">
                        <input type="hidden" name="id" value="<?= (int) $pc['id'] ?>">
                        <button type="submit" title="Supprimer"
                                class="w-10 h-10 rounded-xl bg-red-50 text-red-600 hover:bg-red-100 flex items-center justify-center transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.9 12a2 2 0 0 1-2 1.9H7.9a2 2 0 0 1-2-1.9L5 7m5 4v6m4-6v6m1-10V4a1 1 0 0 0-1-1h-4a1 1 0 0 0-1 1v3m-4 0h14"/></svg>
                        </button>
                    </form>
                </div>
            </li>
            <?php endforeach; ?>
            <?php if (empty($ordinateurs)): ?>
            <li class="px-6 py-14 text-center text-slate-400 text-sm">Aucun ordinateur. Utilisez le formulaire pour en ajouter.</li>
            <?php endif; ?>
        </ul>
    </section>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.confirmation-suppression').forEach((form) => {
        form.addEventListener('submit', (e) => {
            if (!confirm(form.dataset.message || 'Confirmer la suppression ?')) {
                e.preventDefault();
            }
        });
    });
});
</script>

<?php require __DIR__ . '/pied.php'; ?>
