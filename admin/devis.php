<?php
/**
 * Gestion des devis : demandes reçues du site + création de devis PDF.
 */
require_once __DIR__ . '/garde.php';
$titre_admin = 'Devis';

$notification = '';
$type_notification = 'succes';

/* ---------- Traitement des actions ---------- */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && csrf_valide($_POST['csrf_token'] ?? null)) {
    $action = $_POST['action'] ?? '';

    if ($action === 'creer' || $action === 'modifier') {
        $id               = (int) ($_POST['id'] ?? 0);
        $client_nom       = trim($_POST['client_nom'] ?? '');
        $client_telephone = trim($_POST['client_telephone'] ?? '');
        $client_email     = trim($_POST['client_email'] ?? '');
        $objet            = trim($_POST['objet'] ?? '');

        $designations     = $_POST['designation'] ?? [];
        $quantites        = $_POST['quantite'] ?? [];
        $prix_unitaires   = $_POST['prix_unitaire'] ?? [];

        if ($client_nom === '' || $client_telephone === '') {
            $notification = 'Le nom et le téléphone du client sont obligatoires.';
            $type_notification = 'erreur';
        } else {
            $lignes = [];
            $total = 0;
            foreach ($designations as $i => $designation) {
                $designation = trim($designation);
                if ($designation === '') continue;
                $quantite = max(0, (float) ($quantites[$i] ?? 1));
                $pu = max(0, (float) ($prix_unitaires[$i] ?? 0));
                $lignes[] = [$designation, $quantite, $pu];
                $total += $quantite * $pu;
            }
            if (empty($lignes)) {
                $notification = 'Ajoutez au moins une ligne au devis.';
                $type_notification = 'erreur';
            } else {
                $pdo = db();
                if ($action === 'creer') {
                    $numero = generer_numero_devis();
                    $pdo->prepare('INSERT INTO devis (numero, client_nom, client_telephone, client_email, objet, total) VALUES (?, ?, ?, ?, ?, ?)')
                        ->execute([$numero, $client_nom, $client_telephone, $client_email ?: null, $objet ?: null, $total]);
                    $id = (int) $pdo->lastInsertId();
                    $notification = "Devis $numero créé avec succès.";
                } else {
                    $pdo->prepare('UPDATE devis SET client_nom = ?, client_telephone = ?, client_email = ?, objet = ?, total = ? WHERE id = ?')
                        ->execute([$client_nom, $client_telephone, $client_email ?: null, $objet ?: null, $total, $id]);
                    $pdo->prepare('DELETE FROM devis_lignes WHERE devis_id = ?')->execute([$id]);
                    $notification = 'Devis modifié avec succès.';
                }
                $stmt_ligne = $pdo->prepare('INSERT INTO devis_lignes (devis_id, designation, quantite, prix_unitaire) VALUES (?, ?, ?, ?)');
                foreach ($lignes as [$designation, $quantite, $pu]) {
                    $stmt_ligne->execute([$id, $designation, $quantite, $pu]);
                }
            }
        }
    } elseif ($action === 'supprimer') {
        db()->prepare('DELETE FROM devis WHERE id = ?')->execute([(int) ($_POST['id'] ?? 0)]);
        $notification = 'Devis supprimé.';
    } elseif ($action === 'statut') {
        $statut = in_array($_POST['statut'] ?? '', ['brouillon', 'envoye', 'accepte', 'refuse'], true) ? $_POST['statut'] : 'brouillon';
        db()->prepare('UPDATE devis SET statut = ? WHERE id = ?')->execute([$statut, (int) ($_POST['id'] ?? 0)]);
        $notification = 'Statut mis à jour.';
    } elseif ($action === 'marquer_traitee') {
        db()->prepare('UPDATE demandes_devis SET traitee = 1 - traitee WHERE id = ?')->execute([(int) ($_POST['id'] ?? 0)]);
        $notification = 'Demande mise à jour.';
    } elseif ($action === 'supprimer_demande') {
        db()->prepare('DELETE FROM demandes_devis WHERE id = ?')->execute([(int) ($_POST['id'] ?? 0)]);
        $notification = 'Demande supprimée.';
    }
}

/* ---------- Données ---------- */
$devis_liste = db()->query('SELECT * FROM devis ORDER BY cree_le DESC')->fetchAll();
$demandes = db()->query('SELECT * FROM demandes_devis ORDER BY traitee ASC, cree_le DESC')->fetchAll();

$en_edition = null;
if (isset($_GET['modifier'])) {
    $en_edition = get_devis((int) $_GET['modifier']);
}
$prefill = null;
if (isset($_GET['nouveau'])) {
    $prefill = ['client_nom' => '', 'client_telephone' => '', 'client_email' => '', 'objet' => ''];
    if (isset($_GET['demande'])) {
        $stmt = db()->prepare('SELECT * FROM demandes_devis WHERE id = ?');
        $stmt->execute([(int) $_GET['demande']]);
        if ($d = $stmt->fetch()) {
            $prefill = [
                'client_nom' => $d['nom'],
                'client_telephone' => $d['telephone'],
                'client_email' => $d['email'] ?? '',
                'objet' => $d['service'],
            ];
        }
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

<?php if ($en_edition || $prefill): ?>
<!-- ===================== FORMULAIRE DEVIS ===================== -->
<section class="mb-12 bg-white rounded-2xl shadow-sm border border-slate-100 p-8">
    <div class="flex items-center justify-between mb-6">
        <h2 class="font-bold text-slate-900 text-lg"><?= $en_edition ? 'Modifier le devis ' . e($en_edition['numero']) : 'Créer un nouveau devis' ?></h2>
        <a href="devis.php" class="text-slate-400 hover:text-slate-600 text-sm font-semibold">✕ Fermer</a>
    </div>

    <form method="post" action="devis.php" id="formulaire-devis">
        <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
        <input type="hidden" name="action" value="<?= $en_edition ? 'modifier' : 'creer' ?>">
        <input type="hidden" name="id" value="<?= $en_edition ? (int) $en_edition['id'] : '' ?>">

        <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-8">
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-2">Client *</label>
                <input type="text" name="client_nom" required maxlength="150"
                       value="<?= e($en_edition['client_nom'] ?? $prefill['client_nom']) ?>"
                       class="w-full rounded-xl border border-slate-200 px-4 py-3 focus:outline-none focus:ring-2 focus:ring-dsi-500 transition"
                       placeholder="Nom du client">
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-2">Téléphone *</label>
                <input type="tel" name="client_telephone" required maxlength="30"
                       value="<?= e($en_edition['client_telephone'] ?? $prefill['client_telephone']) ?>"
                       class="w-full rounded-xl border border-slate-200 px-4 py-3 focus:outline-none focus:ring-2 focus:ring-dsi-500 transition"
                       placeholder="0X XX XX XX XX">
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-2">E-mail</label>
                <input type="email" name="client_email" maxlength="150"
                       value="<?= e($en_edition['client_email'] ?? $prefill['client_email']) ?>"
                       class="w-full rounded-xl border border-slate-200 px-4 py-3 focus:outline-none focus:ring-2 focus:ring-dsi-500 transition"
                       placeholder="optionnel">
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-2">Objet</label>
                <input type="text" name="objet" maxlength="200"
                       value="<?= e($en_edition['objet'] ?? $prefill['objet']) ?>"
                       class="w-full rounded-xl border border-slate-200 px-4 py-3 focus:outline-none focus:ring-2 focus:ring-dsi-500 transition"
                       placeholder="Ex : Installation caméras">
            </div>
        </div>

        <h3 class="font-bold text-slate-900 mb-4">Prestations</h3>
        <div class="space-y-3" id="conteneur-lignes">
            <?php
            $lignes_init = $en_edition['lignes'] ?? [['designation' => '', 'quantite' => 1, 'prix_unitaire' => 0]];
            foreach ($lignes_init as $ligne): ?>
            <div class="ligne-devis grid grid-cols-12 gap-3 items-center">
                <input type="text" name="designation[]" required maxlength="255"
                       value="<?= e($ligne['designation']) ?>"
                       class="col-span-12 sm:col-span-6 rounded-xl border border-slate-200 px-4 py-3 focus:outline-none focus:ring-2 focus:ring-dsi-500 transition"
                       placeholder="Désignation (ex : Caméra HD + câblage)">
                <input type="number" name="quantite[]" min="0" step="0.01" value="<?= e($ligne['quantite']) ?>"
                       class="col-span-4 sm:col-span-2 calc-total rounded-xl border border-slate-200 px-4 py-3 focus:outline-none focus:ring-2 focus:ring-dsi-500 transition"
                       placeholder="Qté">
                <input type="number" name="prix_unitaire[]" min="0" step="0.01" value="<?= e($ligne['prix_unitaire']) ?>"
                       class="col-span-6 sm:col-span-3 calc-total rounded-xl border border-slate-200 px-4 py-3 focus:outline-none focus:ring-2 focus:ring-dsi-500 transition"
                       placeholder="Prix unitaire (FCFA)">
                <button type="button" class="col-span-2 sm:col-span-1 supprimer-ligne w-10 h-10 rounded-xl bg-red-50 text-red-500 hover:bg-red-100 flex items-center justify-center transition mx-auto" title="Supprimer la ligne">✕</button>
            </div>
            <?php endforeach; ?>
        </div>

        <div class="mt-4 flex flex-wrap items-center justify-between gap-4">
            <button type="button" id="ajouter-ligne"
                    class="inline-flex items-center gap-2 border-2 border-dashed border-dsi-300 text-dsi-600 hover:bg-dsi-50 font-semibold px-5 py-2.5 rounded-xl transition text-sm">
                + Ajouter une ligne
            </button>
            <p class="text-lg font-extrabold text-slate-900">Total : <span id="total-devis" class="text-dsi-700">0 FCFA</span></p>
        </div>

        <div class="mt-8 pt-6 border-t border-slate-100 flex gap-3">
            <button type="submit"
                    class="bg-dsi-600 hover:bg-dsi-500 text-white font-semibold px-8 py-3.5 rounded-xl transition shadow-lg shadow-dsi-200">
                <?= $en_edition ? 'Enregistrer les modifications' : 'Créer le devis' ?>
            </button>
            <a href="devis.php" class="px-6 py-3.5 rounded-xl border border-slate-200 text-slate-500 hover:bg-slate-50 font-semibold transition">Annuler</a>
        </div>
    </form>
</div>
<script>
document.addEventListener('DOMContentLoaded', () => {
    const conteneur = document.getElementById('conteneur-lignes');

    const formaterFcfa = (n) => new Intl.NumberFormat('fr-FR').format(Math.round(n)) + ' FCFA';
    const majTotal = () => {
        let total = 0;
        document.querySelectorAll('.ligne-devis').forEach((ligne) => {
            const qte = parseFloat(ligne.querySelector('[name="quantite[]"]').value) || 0;
            const pu = parseFloat(ligne.querySelector('[name="prix_unitaire[]"]').value) || 0;
            total += qte * pu;
        });
        document.getElementById('total-devis').textContent = formaterFcfa(total);
    };

    document.getElementById('ajouter-ligne').addEventListener('click', () => {
        const modele = conteneur.querySelector('.ligne-devis');
        const nouvelle = modele.cloneNode(true);
        nouvelle.querySelectorAll('input').forEach((input) => {
            if (input.name === 'designation[]') input.value = '';
            if (input.name === 'quantite[]') input.value = 1;
            if (input.name === 'prix_unitaire[]') input.value = 0;
        });
        conteneur.appendChild(nouvelle);
        majTotal();
    });

    conteneur.addEventListener('input', (e) => { if (e.target.classList.contains('calc-total')) majTotal(); });
    conteneur.addEventListener('click', (e) => {
        if (e.target.classList.contains('supprimer-ligne')) {
            if (conteneur.querySelectorAll('.ligne-devis').length > 1) {
                e.target.closest('.ligne-devis').remove();
                majTotal();
            }
        }
    });
    majTotal();
});
</script>

<?php else: ?>

<!-- ===================== DEMANDES RECUES ===================== -->
<section class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden mb-10">
    <header class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
        <h2 class="font-bold text-slate-900">Demandes de devis reçues (<?= compter_demandes_devis_non_traitees() ?> non traitées)</h2>
        <a href="devis.php?nouveau=1" class="text-sm bg-dsi-600 hover:bg-dsi-500 text-white font-semibold px-4 py-2 rounded-lg transition">+ Nouveau devis</a>
    </header>
    <ul class="divide-y divide-slate-100">
        <?php foreach ($demandes as $demande): ?>
        <li class="px-6 py-5">
            <div class="flex flex-col lg:flex-row lg:items-center gap-4">
                <span class="w-2.5 h-2.5 rounded-full shrink-0 <?= $demande['traitee'] ? 'bg-slate-200' : 'bg-amber-400' ?>"></span>
                <div class="min-w-0 flex-1">
                    <p class="font-bold text-slate-800">
                        <?= e($demande['nom']) ?>
                        <span class="text-slate-400 font-medium text-sm">— <?= e($demande['service']) ?></span>
                        <?= $demande['traitee'] ? '' : '<span class="ml-2 px-2 py-0.5 rounded-full bg-amber-50 text-amber-600 text-xs font-bold uppercase">Nouvelle</span>' ?>
                    </p>
                    <p class="text-sm text-slate-500 mt-1"><?= e($demande['description']) ?></p>
                    <p class="text-xs text-slate-400 mt-1.5">
                        📞 <a href="tel:<?= e($demande['telephone']) ?>" class="hover:underline"><?= e($demande['telephone']) ?></a>
                        <?php if ($demande['email']): ?> · ✉️ <?= e($demande['email']) ?><?php endif; ?>
                        · <?= e(date_fr($demande['cree_le'])) ?>
                    </p>
                </div>
                <div class="flex items-center gap-2 shrink-0">
                    <a href="devis.php?nouveau=1&demande=<?= (int) $demande['id'] ?>"
                       class="bg-dsi-600 hover:bg-dsi-500 text-white text-sm font-semibold px-4 py-2.5 rounded-xl transition">Créer le devis</a>
                    <form method="post" action="devis.php">
                        <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                        <input type="hidden" name="action" value="marquer_traitee">
                        <input type="hidden" name="id" value="<?= (int) $demande['id'] ?>">
                        <button type="submit" class="border border-slate-200 text-slate-500 hover:bg-slate-50 text-sm font-semibold px-4 py-2.5 rounded-xl transition">
                            <?= $demande['traitee'] ? 'Non traitée' : 'Traitée ✓' ?>
                        </button>
                    </form>
                    <form method="post" action="devis.php" class="confirmation-suppression" data-message="Supprimer cette demande ?">
                        <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                        <input type="hidden" name="action" value="supprimer_demande">
                        <input type="hidden" name="id" value="<?= (int) $demande['id'] ?>">
                        <button type="submit" class="bg-red-50 text-red-600 hover:bg-red-100 text-sm font-semibold px-3 py-2.5 rounded-xl transition">🗑</button>
                    </form>
                </div>
            </div>
        </li>
        <?php endforeach; ?>
        <?php if (empty($demandes)): ?>
        <li class="px-6 py-12 text-center text-slate-400 text-sm">Aucune demande de devis reçue pour le moment.</li>
        <?php endif; ?>
    </ul>
</section>

<!-- ===================== LISTE DES DEVIS ===================== -->
<section class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
    <header class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
        <h2 class="font-bold text-slate-900">Devis créés (<?= count($devis_liste) ?>)</h2>
        <a href="devis.php?nouveau=1" class="text-sm bg-dsi-600 hover:bg-dsi-500 text-white font-semibold px-4 py-2 rounded-lg transition">+ Nouveau devis</a>
    </header>
    <ul class="divide-y divide-slate-100">
        <?php foreach ($devis_liste as $d): ?>
        <?php [$libelle_statut, $classe_statut] = statut_devis($d['statut']); ?>
        <li class="px-6 py-5 flex flex-col lg:flex-row lg:items-center gap-4">
            <div class="min-w-0 flex-1">
                <div class="flex items-center gap-2 flex-wrap">
                    <p class="font-bold text-slate-800 font-mono"><?= e($d['numero']) ?></p>
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold <?= $classe_statut ?>"><?= e($libelle_statut) ?></span>
                </div>
                <p class="text-sm text-slate-500 mt-1">
                    <?= e($d['client_nom']) ?> · 📞 <?= e($d['client_telephone']) ?>
                    <?php if ($d['objet']): ?> · <?= e($d['objet']) ?><?php endif; ?>
                </p>
                <p class="text-sm font-bold text-dsi-700 mt-1"><?= number_format((float) $d['total'], 0, ',', ' ') ?> FCFA</p>
            </div>
            <div class="flex flex-wrap items-center gap-2 shrink-0">
                <!-- Changer le statut -->
                <form method="post" action="devis.php" class="flex">
                    <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                    <input type="hidden" name="action" value="statut">
                    <input type="hidden" name="id" value="<?= (int) $d['id'] ?>">
                    <select name="statut" onchange="this.form.submit()"
                            class="rounded-xl border border-slate-200 px-3 py-2.5 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-dsi-500 transition">
                        <option value="brouillon" <?= $d['statut'] === 'brouillon' ? 'selected' : '' ?>>Brouillon</option>
                        <option value="envoye" <?= $d['statut'] === 'envoye' ? 'selected' : '' ?>>Envoyé</option>
                        <option value="accepte" <?= $d['statut'] === 'accepte' ? 'selected' : '' ?>>Accepté</option>
                        <option value="refuse" <?= $d['statut'] === 'refuse' ? 'selected' : '' ?>>Refusé</option>
                    </select>
                </form>
                <a href="devis-impression.php?id=<?= (int) $d['id'] ?>" target="_blank"
                   class="bg-dsi-50 text-dsi-600 hover:bg-dsi-100 text-sm font-semibold px-4 py-2.5 rounded-xl transition">📄 PDF</a>
                <a href="devis.php?modifier=<?= (int) $d['id'] ?>"
                   class="border border-slate-200 text-slate-500 hover:bg-slate-50 text-sm font-semibold px-4 py-2.5 rounded-xl transition">Modifier</a>
                <form method="post" action="devis.php" class="confirmation-suppression" data-message="Supprimer le devis <?= e($d['numero']) ?> ?">
                    <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                    <input type="hidden" name="action" value="supprimer">
                    <input type="hidden" name="id" value="<?= (int) $d['id'] ?>">
                    <button type="submit" class="bg-red-50 text-red-600 hover:bg-red-100 text-sm font-semibold px-3 py-2.5 rounded-xl transition">🗑</button>
                </form>
            </div>
        </li>
        <?php endforeach; ?>
        <?php if (empty($devis_liste)): ?>
        <li class="px-6 py-12 text-center text-slate-400 text-sm">Aucun devis créé. Cliquez sur « + Nouveau devis » pour commencer.</li>
        <?php endif; ?>
    </ul>
</section>
<?php endif; ?>

<script>
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.confirmation-suppression').forEach((form) => {
        form.addEventListener('submit', (e) => {
            if (!confirm(form.dataset.message || 'Confirmer la suppression ?')) e.preventDefault();
        });
    });
});
</script>

<?php require __DIR__ . '/pied.php'; ?>
