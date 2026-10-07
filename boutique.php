<?php
$titre_page = 'Vente d\'ordinateurs';
require_once __DIR__ . '/includes/functions.php';
$ordinateurs = get_ordinateurs();
require __DIR__ . '/includes/header.php';
?>

<!-- En-tête de page -->
<section class="pt-40 pb-20 bg-dsi-950 relative overflow-hidden">
    <div class="absolute -top-24 left-0 w-96 h-96 bg-dsi-600/30 rounded-full blur-3xl"></div>
    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <span class="text-dsi-300 font-semibold text-sm uppercase tracking-widest">Boutique</span>
        <h1 class="mt-3 text-4xl sm:text-5xl font-extrabold text-white">Vente d'ordinateurs</h1>
        <p class="mt-5 text-slate-300 max-w-2xl mx-auto leading-relaxed">
            Découvrez notre sélection d'ordinateurs portables et de bureau,
            testés et garantis. Contactez-nous sur WhatsApp pour commander.
        </p>
    </div>
</section>

<!-- Catalogue -->
<section class="py-24 bg-slate-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-8">
            <?php foreach ($ordinateurs as $pc): ?>
            <article class="group bg-white rounded-2xl overflow-hidden border border-slate-100 hover:border-dsi-200 hover:shadow-xl hover:shadow-dsi-100 hover:-translate-y-1 transition-all duration-300 flex flex-col reveal">
                <div class="relative overflow-hidden bg-white border-b border-slate-100 flex items-center justify-center">
                    <img src="<?= e($pc['image']) ?>" alt="<?= e($pc['nom']) ?>"
                         class="w-full h-48 sm:h-52 object-contain p-4 group-hover:scale-105 transition-transform duration-500">
                    <?php if ($pc['prix'] !== null && $pc['prix'] !== ''): ?>
                    <span class="absolute top-4 right-4 bg-dsi-600 text-white text-sm font-bold px-4 py-1.5 rounded-full shadow-lg">
                        <?= formater_prix($pc['prix'], $pc['devise']) ?>
                    </span>
                    <?php endif; ?>
                </div>

                <div class="p-6 flex flex-col flex-1">
                    <h2 class="text-lg font-bold text-slate-900 mb-2"><?= e($pc['nom']) ?></h2>
                    <p class="text-slate-500 text-sm leading-relaxed flex-1"><?= e($pc['description']) ?></p>

                    <div class="mt-5 pt-5 border-t border-slate-100 flex items-center justify-between gap-3">
                        <?php if ($pc['prix'] !== null && $pc['prix'] !== ''): ?>
                        <p class="font-extrabold text-dsi-700 text-lg"><?= formater_prix($pc['prix'], $pc['devise']) ?></p>
                        <?php else: ?>
                        <p class="text-slate-400 font-semibold text-sm">Prix à consulter</p>
                        <?php endif; ?>
                        <a href="<?= e(WHATSAPP_LIEN) ?>?text=<?= rawurlencode('Bonjour, je suis intéressé(e) par : ' . $pc['nom']) ?>"
                           target="_blank" rel="noopener"
                           class="inline-flex items-center gap-2 bg-emerald-500 hover:bg-emerald-600 text-white text-sm font-semibold px-4 py-2.5 rounded-xl transition shadow-lg shadow-emerald-200">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12.04 2c-5.46 0-9.91 4.45-9.91 9.91 0 1.75.46 3.45 1.32 4.95L2.05 22l5.25-1.38a9.87 9.87 0 0 0 4.74 1.21h.01c5.46 0 9.9-4.45 9.9-9.91 0-2.65-1.03-5.14-2.9-7.01A9.82 9.82 0 0 0 12.04 2zm0 18.03h-.01a8.2 8.2 0 0 1-4.19-1.15l-.3-.18-3.12.82.83-3.04-.2-.31a8.2 8.2 0 0 1-1.26-4.38c0-4.54 3.7-8.24 8.25-8.24 2.2 0 4.27.86 5.82 2.42a8.18 8.18 0 0 1 2.41 5.83c0 4.54-3.7 8.23-8.23 8.23zm5.22-6.09c-.25-.13-1.47-.72-1.69-.81-.23-.08-.39-.12-.56.13-.16.24-.64.8-.78.97-.14.16-.29.18-.54.06-.25-.13-1.05-.39-2-1.23-.73-.66-1.23-1.47-1.38-1.72-.14-.24-.01-.38.11-.5.11-.11.25-.29.37-.43.12-.14.16-.25.25-.41.08-.17.04-.31-.02-.43-.06-.13-.56-1.34-.76-1.84-.2-.48-.41-.42-.56-.43h-.48c-.17 0-.43.06-.66.31-.22.24-.86.85-.86 2.07 0 1.22.89 2.4 1.01 2.56.12.17 1.75 2.67 4.23 3.74.59.26 1.05.41 1.41.52.6.19 1.13.16 1.56.1.48-.07 1.47-.6 1.67-1.18.21-.58.21-1.07.15-1.18-.06-.1-.23-.16-.48-.29z"/></svg>
                            Commander
                        </a>
                    </div>
                </div>
            </article>
            <?php endforeach; ?>
        </div>

        <?php if (empty($ordinateurs)): ?>
        <p class="text-center text-slate-500 py-12">Aucun ordinateur en vente pour le moment.</p>
        <?php endif; ?>

        <!-- Note -->
        <div class="mt-16 bg-white border border-slate-100 rounded-2xl p-8 text-center reveal">
            <p class="text-slate-600 leading-relaxed">
                💡 Tous nos ordinateurs sont <strong>testés et garantis</strong>.
                Pour plus de détails sur une machine ou pour négocier, écrivez-nous directement sur
                <a href="<?= e(WHATSAPP_LIEN) ?>" target="_blank" rel="noopener" class="text-emerald-600 font-semibold hover:underline">WhatsApp</a>
                ou par <a href="mailto:<?= e(SITE_EMAIL) ?>" class="text-dsi-600 font-semibold hover:underline">e-mail</a>.
            </p>
        </div>
    </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
