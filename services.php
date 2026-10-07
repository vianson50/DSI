<?php
$titre_page = 'Nos services';
require_once __DIR__ . '/includes/functions.php';
$services = get_services();
require __DIR__ . '/includes/header.php';
?>

<!-- En-tête de page -->
<section class="pt-40 pb-20 bg-dsi-950 relative overflow-hidden">
    <div class="absolute -top-24 right-0 w-96 h-96 bg-dsi-600/30 rounded-full blur-3xl"></div>
    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <span class="text-dsi-300 font-semibold text-sm uppercase tracking-widest">Ce que nous faisons</span>
        <h1 class="mt-3 text-4xl sm:text-5xl font-extrabold text-white">Nos services</h1>
        <p class="mt-5 text-slate-300 max-w-2xl mx-auto leading-relaxed">
            Des solutions complètes et évolutives pour répondre à tous vos défis informatiques,
            du quotidien aux projets les plus ambitieux.
        </p>
    </div>
</section>

<!-- Liste des services -->
<section class="py-24 bg-slate-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-16">
        <?php foreach ($services as $index => $service): ?>
        <article id="service-<?= (int) $service['id'] ?>" class="grid lg:grid-cols-2 gap-12 items-center <?= $index % 2 === 1 ? 'lg:[&>*:first-child]:order-2' : '' ?>">
            <div class="reveal group">
                <div class="relative rounded-3xl bg-gradient-to-br from-dsi-700 via-dsi-600 to-dsi-500 p-10 shadow-xl shadow-dsi-200 overflow-hidden h-80 flex flex-col items-center justify-center text-center">
                    <div class="absolute inset-0 bg-grid opacity-10"></div>
                    <span class="w-2 h-2 rounded-full bg-white/20 absolute top-8 left-8"></span>
                    <span class="w-3 h-3 rounded-full bg-white/20 absolute bottom-10 right-10"></span>
                    <div class="relative w-24 h-24 rounded-3xl bg-white/15 backdrop-blur border border-white/20 text-white flex items-center justify-center shadow-2xl group-hover:scale-110 group-hover:rotate-3 transition-transform duration-500 [&_svg]:w-12 [&_svg]:h-12">
                        <?= icone_svg($service['icone']) ?>
                    </div>
                    <p class="relative mt-6 text-white/90 font-semibold tracking-wide uppercase text-xs">Service <?= str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT) ?></p>
                </div>
            </div>
            <div class="reveal">
                <span class="text-dsi-600 font-bold text-5xl opacity-20">0<?= $index + 1 ?></span>
                <h2 class="mt-2 text-2xl sm:text-3xl font-extrabold text-slate-900"><?= e($service['titre']) ?></h2>
                <p class="mt-4 text-slate-500 leading-relaxed text-lg"><?= e($service['description']) ?></p>
                <a href="contact.php" class="mt-6 inline-flex items-center gap-2 text-dsi-600 font-semibold hover:gap-3 transition-all">
                    Demander ce service
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5-5 5M6 12h12"/></svg>
                </a>
            </div>
        </article>
        <?php endforeach; ?>

        <?php if (empty($services)): ?>
        <p class="text-center text-slate-500 py-12">Les services seront bientôt disponibles.</p>
        <?php endif; ?>
    </div>
</section>

<!-- Appel à l'action -->
<section class="py-20 bg-dsi-950">
    <div class="max-w-4xl mx-auto px-4 text-center reveal">
        <h2 class="text-3xl font-extrabold text-white">Un besoin spécifique ?</h2>
        <p class="mt-4 text-slate-300">Nous étudions chaque projet avec attention. Parlons du vôtre.</p>
        <a href="contact.php" class="mt-8 inline-flex items-center gap-2 bg-dsi-600 hover:bg-dsi-500 text-white font-semibold px-10 py-4 rounded-full transition shadow-xl">
            Nous contacter
        </a>
    </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
