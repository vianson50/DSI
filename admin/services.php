<?php
/**
 * Gestion des services : ajout, modification, activation, suppression.
 */
require_once __DIR__ . '/garde.php';
$titre_admin = 'Gestion des services';

$notification = '';
$type_notification = 'succes';

$icones_disponibles = ['camera', 'laptop', 'wrench', 'phone', 'clipboard', 'shield', 'disk', 'cpu', 'printer', 'card', 'code', 'network', 'lightbulb', 'headset', 'cloud', 'database'];

// Traitement des actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_valide($_POST['csrf_token'] ?? null)) {
        $notification = 'Session expirée, veuillez réessayer.';
        $type_notification = 'erreur';
    } else {
        $action = $_POST['action'] ?? '';

        if ($action === 'creer' || $action === 'modifier') {
            $id          = (int) ($_POST['id'] ?? 0);
            $titre       = trim($_POST['titre'] ?? '');
            $description = trim($_POST['description'] ?? '');
            $icone       = in_array($_POST['icone'] ?? '', $icones_disponibles, true) ? $_POST['icone'] : 'code';
            $ordre       = max(0, (int) ($_POST['ordre_affichage'] ?? 0));
            $actif       = isset($_POST['actif']) ? 1 : 0;

            if ($titre === '' || mb_strlen($titre) > 150) {
                $notification = 'Le titre est obligatoire (150 caractères max).';
                $type_notification = 'erreur';
            } elseif ($description === '') {
                $notification = 'La description est obligatoire.';
                $type_notification = 'erreur';
            } else {
                if ($action === 'creer') {
                    db()->prepare('INSERT INTO services (titre, description, image, icone, actif, ordre_affichage) VALUES (?, ?, ?, ?, ?, ?)')
                        ->execute([$titre, $description, 'assets/img/service-1.jpg', $icone, $actif, $ordre]);
                    $notification = 'Service ajouté avec succès.';
                } else {
                    db()->prepare('UPDATE services SET titre = ?, description = ?, icone = ?, actif = ?, ordre_affichage = ? WHERE id = ?')
                        ->execute([$titre, $description, $icone, $actif, $ordre, $id]);
                    $notification = 'Service modifié avec succès.';
                }
            }
        } elseif ($action === 'supprimer') {
            db()->prepare('DELETE FROM services WHERE id = ?')->execute([(int) ($_POST['id'] ?? 0)]);
            $notification = 'Service supprimé.';
        } elseif ($action === 'basculer') {
            db()->prepare('UPDATE services SET actif = 1 - actif WHERE id = ?')->execute([(int) ($_POST['id'] ?? 0)]);
            $notification = 'Statut du service mis à jour.';
        }
    }
}

$services  = get_services(true);
$en_edition = null;
if (isset($_GET['modifier'])) {
    $en_edition = get_service((int) $_GET['modifier']);
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
        <h2 class="font-bold text-slate-900 mb-6"><?= $en_edition ? 'Modifier le service' : 'Ajouter un service' ?></h2>

        <form method="post" action="services.php" class="space-y-5">
            <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
            <input type="hidden" name="action" value="<?= $en_edition ? 'modifier' : 'creer' ?>">
            <input type="hidden" name="id" value="<?= $en_edition ? (int) $en_edition['id'] : '' ?>">

            <div>
                <label for="titre" class="block text-sm font-semibold text-slate-700 mb-2">Titre *</label>
                <input type="text" id="titre" name="titre" required maxlength="150"
                       value="<?= e($en_edition['titre'] ?? '') ?>"
                       class="w-full rounded-xl border border-slate-200 px-4 py-3 focus:outline-none focus:ring-2 focus:ring-dsi-500 focus:border-dsi-500 transition"
                       placeholder="Ex : Développement Web">
            </div>

            <div>
                <label for="description" class="block text-sm font-semibold text-slate-700 mb-2">Description *</label>
                <textarea id="description" name="description" required rows="4"
                          class="w-full rounded-xl border border-slate-200 px-4 py-3 focus:outline-none focus:ring-2 focus:ring-dsi-500 focus:border-dsi-500 transition resize-y"
                          placeholder="Description du service…"><?= e($en_edition['description'] ?? '') ?></textarea>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label for="icone" class="block text-sm font-semibold text-slate-700 mb-2">Icône</label>
                    <select id="icone" name="icone"
                            class="w-full rounded-xl border border-slate-200 px-4 py-3 focus:outline-none focus:ring-2 focus:ring-dsi-500 transition bg-white">
                        <?php foreach ($icones_disponibles as $ic): ?>
                        <option value="<?= e($ic) ?>" <?= ($en_edition['icone'] ?? '') === $ic ? 'selected' : '' ?>><?= e($ic) ?></option>
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
                <span class="text-sm font-semibold text-slate-700">Service actif (visible sur le site)</span>
            </label>

            <div class="flex gap-3">
                <button type="submit"
                        class="flex-1 bg-dsi-600 hover:bg-dsi-500 text-white font-semibold py-3.5 rounded-xl transition shadow-lg shadow-dsi-200">
                    <?= $en_edition ? 'Enregistrer' : 'Ajouter' ?>
                </button>
                <?php if ($en_edition): ?>
                <a href="services.php" class="px-5 py-3.5 rounded-xl border border-slate-200 text-slate-500 hover:bg-slate-50 font-semibold transition">Annuler</a>
                <?php endif; ?>
            </div>
        </form>
    </section>

    <!-- Liste -->
    <section class="lg:col-span-2 bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
        <header class="px-6 py-4 border-b border-slate-100">
            <h2 class="font-bold text-slate-900">Services (<?= count($services) ?>)</h2>
        </header>
        <ul class="divide-y divide-slate-100">
            <?php foreach ($services as $srv): ?>
            <li class="px-6 py-5 flex flex-col sm:flex-row sm:items-center gap-4">
                <span class="w-14 h-14 rounded-xl bg-gradient-to-br from-dsi-600 to-dsi-500 text-white flex items-center justify-center shrink-0 [&_svg]:w-7 [&_svg]:h-7">
                    <?= icone_svg($srv['icone']) ?>
                </span>
                <div class="min-w-0 flex-1">
                    <div class="flex items-center gap-2 flex-wrap">
                        <p class="font-bold text-slate-800"><?= e($srv['titre']) ?></p>
                        <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold
                            <?= $srv['actif'] ? 'bg-emerald-50 text-emerald-700' : 'bg-slate-100 text-slate-500' ?>">
                            <?= $srv['actif'] ? 'Actif' : 'Inactif' ?>
                        </span>
                    </div>
                    <p class="text-sm text-slate-400 mt-1 line-clamp-2"><?= e($srv['description']) ?></p>
                </div>
                <div class="flex items-center gap-2 shrink-0">
                    <!-- Activer / désactiver -->
                    <form method="post" action="services.php">
                        <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                        <input type="hidden" name="action" value="basculer">
                        <input type="hidden" name="id" value="<?= (int) $srv['id'] ?>">
                        <button type="submit" title="<?= $srv['actif'] ? 'Désactiver' : 'Activer' ?>"
                                class="w-10 h-10 rounded-xl flex items-center justify-center transition
                                <?= $srv['actif'] ? 'bg-amber-50 text-amber-600 hover:bg-amber-100' : 'bg-emerald-50 text-emerald-600 hover:bg-emerald-100' ?>">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <?php if ($srv['actif']): ?>
                                <path stroke-linecap="round" d="M10.5 21h-3a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h3m4 0h3a2 2 0 0 1 2 2v3M9 12h12m-3-3l3 3-3 3" stroke-linejoin="round"/>
                                <?php else: ?>
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                <?php endif; ?>
                            </svg>
                        </button>
                    </form>
                    <!-- Modifier -->
                    <a href="services.php?modifier=<?= (int) $srv['id'] ?>" title="Modifier"
                       class="w-10 h-10 rounded-xl bg-dsi-50 text-dsi-600 hover:bg-dsi-100 flex items-center justify-center transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5h2m-1 14h8m-8-4l4-8 4 8M3 13l4-8 2.5 5"/></svg>
                    </a>
                    <!-- Supprimer -->
                    <form method="post" action="services.php" class="confirmation-suppression"
                          data-message="Supprimer définitivement « <?= e($srv['titre']) ?> » ?">
                        <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                        <input type="hidden" name="action" value="supprimer">
                        <input type="hidden" name="id" value="<?= (int) $srv['id'] ?>">
                        <button type="submit" title="Supprimer"
                                class="w-10 h-10 rounded-xl bg-red-50 text-red-600 hover:bg-red-100 flex items-center justify-center transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.9 12a2 2 0 0 1-2 1.9H7.9a2 2 0 0 1-2-1.9L5 7m5 4v6m4-6v6m1-10V4a1 1 0 0 0-1-1h-4a1 1 0 0 0-1 1v3m-4 0h14"/></svg>
                        </button>
                    </form>
                </div>
            </li>
            <?php endforeach; ?>
            <?php if (empty($services)): ?>
            <li class="px-6 py-14 text-center text-slate-400 text-sm">Aucun service. Utilisez le formulaire pour en ajouter.</li>
            <?php endif; ?>
        </ul>
    </section>
</div>

<script>
// Confirmation avant suppression
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
