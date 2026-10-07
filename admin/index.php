<?php
/**
 * Tableau de bord admin.
 */
require_once __DIR__ . '/garde.php';
$titre_admin = 'Tableau de bord';

$stats = [
    'services'   => (int) db()->query('SELECT COUNT(*) FROM services')->fetchColumn(),
    'actifs'     => (int) db()->query('SELECT COUNT(*) FROM services WHERE actif = 1')->fetchColumn(),
    'messages'   => (int) db()->query('SELECT COUNT(*) FROM messages')->fetchColumn(),
    'non_lus'    => compter_messages_non_lus(),
];
$derniers_messages = array_slice(get_messages(), 0, 5);
$derniers_services = array_slice(get_services(true), 0, 5);

require __DIR__ . '/entete.php';
?>

<!-- Statistiques -->
<div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-10">
    <?php
    $cartes = [
        ['Services totaux', $stats['services'], 'grid', 'dsi'],
        ['Services actifs', $stats['actifs'], 'check', 'emerald'],
        ['Messages reçus', $stats['messages'], 'mail', 'slate'],
        ['Non lus', $stats['non_lus'], 'bell', $stats['non_lus'] > 0 ? 'amber' : 'slate'],
    ];
    foreach ($cartes as [$libelle, $valeur, $icone, $couleur]): ?>
    <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-100 flex items-center gap-5">
        <div class="w-14 h-14 rounded-xl bg-<?= $couleur ?>-50 text-<?= $couleur ?>-600 flex items-center justify-center shrink-0">
            <svg class="w-7 h-7" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                <?php
                $svg = [
                    'grid'  => '<path stroke-linecap="round" stroke-linejoin="round" d="M4 5h6v6H4zM14 5h6v6h-6zM4 15h6v6H4zM14 15h6v6h-6z"/>',
                    'check' => '<path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 1 1-18 0 9 9 0 0 1 18 0z"/>',
                    'mail'  => '<path stroke-linecap="round" stroke-linejoin="round" d="M3 8l9 6 9-6M3 6h18v12H3V6z"/>',
                    'bell'  => '<path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.4-2A7 7 0 0 0 19 12v-2a7 7 0 1 0-14 0v2c0 1.1-.2 2.1-.6 3L3 17h12zm-6 0v1a3 3 0 0 0 6 0v-1"/>',
                ];
                echo $svg[$icone];
                ?>
            </svg>
        </div>
        <div>
            <p class="text-3xl font-extrabold text-slate-900"><?= (int) $valeur ?></p>
            <p class="text-sm text-slate-500"><?= e($libelle) ?></p>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<div class="grid lg:grid-cols-2 gap-8">
    <!-- Derniers messages -->
    <section class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
        <header class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
            <h2 class="font-bold text-slate-900">Derniers messages</h2>
            <a href="messages.php" class="text-sm text-dsi-600 font-semibold hover:underline">Tout voir</a>
        </header>
        <ul class="divide-y divide-slate-100">
            <?php foreach ($derniers_messages as $msg): ?>
            <li class="px-6 py-4 flex items-start gap-3">
                <span class="w-2.5 h-2.5 rounded-full mt-2 shrink-0 <?= $msg['lu'] ? 'bg-slate-200' : 'bg-dsi-500' ?>"></span>
                <div class="min-w-0">
                    <p class="font-semibold text-slate-800 text-sm truncate">
                        <?= e($msg['sujet']) ?>
                        <?= $msg['lu'] ? '' : '<span class="text-dsi-600 text-xs font-medium">— nouveau</span>' ?>
                    </p>
                    <p class="text-sm text-slate-400 truncate"><?= e($msg['nom']) ?> · <?= e($msg['email']) ?></p>
                </div>
                <span class="ml-auto text-xs text-slate-400 shrink-0"><?= e(date_fr($msg['cree_le'])) ?></span>
            </li>
            <?php endforeach; ?>
            <?php if (empty($derniers_messages)): ?>
            <li class="px-6 py-10 text-center text-slate-400 text-sm">Aucun message reçu pour le moment.</li>
            <?php endif; ?>
        </ul>
    </section>

    <!-- Derniers services -->
    <section class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
        <header class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
            <h2 class="font-bold text-slate-900">Services</h2>
            <a href="services.php" class="text-sm text-dsi-600 font-semibold hover:underline">Gérer</a>
        </header>
        <ul class="divide-y divide-slate-100">
            <?php foreach ($derniers_services as $srv): ?>
            <li class="px-6 py-4 flex items-center gap-4">
                <img src="../assets/img/logo.png" alt="" class="w-12 h-12 rounded-xl object-contain shrink-0">
                <div class="min-w-0">
                    <p class="font-semibold text-slate-800 text-sm truncate"><?= e($srv['titre']) ?></p>
                    <p class="text-xs text-slate-400">Ordre : <?= (int) $srv['ordre_affichage'] ?></p>
                </div>
                <span class="ml-auto px-3 py-1 rounded-full text-xs font-semibold shrink-0
                    <?= $srv['actif'] ? 'bg-emerald-50 text-emerald-700' : 'bg-slate-100 text-slate-500' ?>">
                    <?= $srv['actif'] ? 'Actif' : 'Inactif' ?>
                </span>
            </li>
            <?php endforeach; ?>
            <?php if (empty($derniers_services)): ?>
            <li class="px-6 py-10 text-center text-slate-400 text-sm">Aucun service enregistré.</li>
            <?php endif; ?>
        </ul>
    </section>
</div>

<?php require __DIR__ . '/pied.php'; ?>
