<?php
/**
 * Gestion des messages de contact.
 */
require_once __DIR__ . '/garde.php';
$titre_admin = 'Messages reçus';

$notification = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && csrf_valide($_POST['csrf_token'] ?? null)) {
    $action = $_POST['action'] ?? '';
    $id     = (int) ($_POST['id'] ?? 0);

    if ($action === 'lu') {
        db()->prepare('UPDATE messages SET lu = 1 WHERE id = ?')->execute([$id]);
        $notification = 'Message marqué comme lu.';
    } elseif ($action === 'non_lu') {
        db()->prepare('UPDATE messages SET lu = 0 WHERE id = ?')->execute([$id]);
        $notification = 'Message marqué comme non lu.';
    } elseif ($action === 'supprimer') {
        db()->prepare('DELETE FROM messages WHERE id = ?')->execute([$id]);
        $notification = 'Message supprimé.';
    }
}

$filtre   = $_GET['filtre'] ?? 'tous';
$messages = $filtre === 'non_lus' ? get_messages(true) : get_messages();

require __DIR__ . '/entete.php';
?>

<?php if ($notification): ?>
<div class="mb-8 rounded-xl px-5 py-4 border border-emerald-200 bg-emerald-50 text-emerald-700 text-sm font-medium">
    <?= e($notification) ?>
</div>
<?php endif; ?>

<!-- Filtres -->
<div class="flex gap-3 mb-8">
    <a href="messages.php?filtre=tous"
       class="px-5 py-2.5 rounded-xl text-sm font-semibold transition
       <?= $filtre === 'tous' ? 'bg-dsi-600 text-white shadow-lg shadow-dsi-200' : 'bg-white border border-slate-200 text-slate-500 hover:bg-slate-50' ?>">
        Tous
    </a>
    <a href="messages.php?filtre=non_lus"
       class="px-5 py-2.5 rounded-xl text-sm font-semibold transition
       <?= $filtre === 'non_lus' ? 'bg-dsi-600 text-white shadow-lg shadow-dsi-200' : 'bg-white border border-slate-200 text-slate-500 hover:bg-slate-50' ?>">
        Non lus (<?= compter_messages_non_lus() ?>)
    </a>
</div>

<!-- Liste des messages -->
<div class="space-y-5 max-w-4xl">
    <?php foreach ($messages as $msg): ?>
    <article class="bg-white rounded-2xl shadow-sm border <?= $msg['lu'] ? 'border-slate-100' : 'border-dsi-300 ring-1 ring-dsi-200' ?> p-7">
        <header class="flex flex-wrap items-start justify-between gap-4 mb-4">
            <div>
                <div class="flex items-center gap-3 flex-wrap">
                    <h2 class="font-bold text-slate-900 text-lg"><?= e($msg['sujet']) ?></h2>
                    <?php if (!$msg['lu']): ?>
                    <span class="px-2.5 py-0.5 rounded-full bg-dsi-50 text-dsi-600 text-xs font-bold uppercase tracking-wide">Nouveau</span>
                    <?php endif; ?>
                </div>
                <p class="text-sm text-slate-500 mt-1">
                    <span class="font-semibold text-slate-600"><?= e($msg['nom']) ?></span>
                    · <a href="mailto:<?= e($msg['email']) ?>" class="text-dsi-600 hover:underline"><?= e($msg['email']) ?></a>
                    <?php if ($msg['telephone']): ?>
                    · <a href="tel:<?= e($msg['telephone']) ?>" class="text-dsi-600 hover:underline"><?= e($msg['telephone']) ?></a>
                    <?php endif; ?>
                </p>
            </div>
            <time class="text-xs text-slate-400"><?= e(date_fr($msg['cree_le'])) ?></time>
        </header>

        <p class="text-slate-600 leading-relaxed whitespace-pre-line"><?= e($msg['message']) ?></p>

        <footer class="flex flex-wrap gap-3 mt-6 pt-5 border-t border-slate-100">
            <a href="mailto:<?= e($msg['email']) ?>?subject=Re: <?= e($msg['sujet']) ?>"
               class="inline-flex items-center gap-2 bg-dsi-600 hover:bg-dsi-500 text-white text-sm font-semibold px-5 py-2.5 rounded-xl transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 8l9 6 9-6M3 6h18v12H3V6z"/></svg>
                Répondre
            </a>

            <form method="post" action="messages.php">
                <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                <input type="hidden" name="action" value="<?= $msg['lu'] ? 'non_lu' : 'lu' ?>">
                <input type="hidden" name="id" value="<?= (int) $msg['id'] ?>">
                <button type="submit"
                        class="text-sm font-semibold px-5 py-2.5 rounded-xl border transition
                        <?= $msg['lu'] ? 'border-slate-200 text-slate-500 hover:bg-slate-50' : 'border-dsi-200 text-dsi-600 hover:bg-dsi-50' ?>">
                    <?= $msg['lu'] ? 'Marquer non lu' : 'Marquer comme lu' ?>
                </button>
            </form>

            <form method="post" action="messages.php" class="confirmation-suppression ml-auto"
                  data-message="Supprimer ce message de <?= e($msg['nom']) ?> ?">
                <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                <input type="hidden" name="action" value="supprimer">
                <input type="hidden" name="id" value="<?= (int) $msg['id'] ?>">
                <button type="submit"
                        class="text-sm font-semibold px-5 py-2.5 rounded-xl bg-red-50 text-red-600 hover:bg-red-100 transition">
                    Supprimer
                </button>
            </form>
        </footer>
    </article>
    <?php endforeach; ?>

    <?php if (empty($messages)): ?>
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-14 text-center text-slate-400">
        Aucun message <?= $filtre === 'non_lus' ? 'non lu' : 'reçu' ?> pour le moment.
    </div>
    <?php endif; ?>
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
