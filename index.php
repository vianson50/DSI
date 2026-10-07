<?php
$titre_page = 'Accueil';
require_once __DIR__ . '/includes/functions.php';
$services = get_services();
require __DIR__ . '/includes/header.php';
?>

<!-- Section héro -->
<section class="relative min-h-screen flex items-center overflow-hidden bg-dsi-950">
    <!-- Décor animé -->
    <div class="absolute inset-0">
        <div class="absolute -top-32 -left-32 w-[500px] h-[500px] bg-dsi-600/30 rounded-full blur-3xl animate-pulse-slow"></div>
        <div class="absolute bottom-0 right-0 w-[600px] h-[600px] bg-dsi-500/20 rounded-full blur-3xl"></div>
        <div class="absolute inset-0 bg-grid opacity-[0.15]"></div>
    </div>

    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-32 grid lg:grid-cols-2 gap-16 items-center">
        <div>
            <span class="inline-flex items-center gap-2 bg-dsi-500/10 border border-dsi-500/30 text-dsi-300 px-4 py-1.5 rounded-full text-sm font-medium mb-6">
                <span class="w-2 h-2 bg-emerald-400 rounded-full animate-pulse"></span>
                Disponible pour vos projets
            </span>
            <h1 class="text-4xl sm:text-5xl lg:text-6xl font-extrabold text-white leading-tight">
                Des solutions IT
                <span class="text-transparent bg-clip-text bg-gradient-to-r from-dsi-400 to-dsi-300">sur mesure</span>
                pour votre entreprise
            </h1>
            <p class="mt-6 text-lg text-slate-300 max-w-xl leading-relaxed">
                De la conception de vos applications à la sécurisation de vos infrastructures,
                nous accompagnons votre transformation digitale avec expertise et proximité.
            </p>
            <div class="mt-10 flex flex-wrap gap-4">
                <a href="devis.php" class="inline-flex items-center gap-2 bg-dsi-600 hover:bg-dsi-500 text-white font-semibold px-8 py-4 rounded-full transition shadow-xl shadow-dsi-600/30 hover:-translate-y-0.5">
                    Demander un devis
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5-5 5M6 12h12"/></svg>
                </a>
                <a href="services.php" class="inline-flex items-center gap-2 border border-white/20 hover:border-dsi-400 text-white font-semibold px-8 py-4 rounded-full transition hover:bg-white/5">
                    Découvrir nos services
                </a>
            </div>

            <!-- Statistiques -->
            <div class="mt-14 grid grid-cols-3 gap-6 max-w-md">
                <div>
                    <p class="text-3xl font-extrabold text-white compteur" data-cible="120">0</p>
                    <p class="text-sm text-slate-400 mt-1">Projets livrés</p>
                </div>
                <div>
                    <p class="text-3xl font-extrabold text-white compteur" data-cible="98">0</p>
                    <p class="text-sm text-slate-400 mt-1">% Clients satisfaits</p>
                </div>
                <div>
                    <p class="text-3xl font-extrabold text-white compteur" data-cible="15">0</p>
                    <p class="text-sm text-slate-400 mt-1">Ans d'expérience</p>
                </div>
            </div>
        </div>

        <!-- Carte d'information -->
        <div class="hidden lg:block relative">
            <div class="relative rounded-3xl bg-white/5 backdrop-blur-xl border border-white/10 p-8 shadow-2xl shadow-dsi-900/60">
                <h2 class="text-white font-bold text-lg mb-6">Nos domaines d'expertise</h2>
                <ul class="space-y-1">
                    <?php
                    $expertises = [
                        ['camera', 'Caméras de surveillance'],
                        ['laptop', "Vente d'ordinateurs & accessoires"],
                        ['wrench', 'Maintenance informatique'],
                        ['phone', 'Accessoires de téléphone'],
                        ['printer', "Installation d'imprimantes"],
                    ];
                    foreach ($expertises as [$icone, $libelle]): ?>
                    <li class="flex items-center gap-4 rounded-xl px-4 py-3.5 hover:bg-white/5 transition group cursor-default">
                        <span class="w-11 h-11 rounded-xl bg-gradient-to-br from-dsi-600 to-dsi-500 text-white flex items-center justify-center shrink-0 shadow-lg shadow-dsi-600/40 group-hover:scale-110 transition-transform">
                            <?= icone_svg($icone) ?>
                        </span>
                        <span class="text-slate-200 font-medium"><?= e($libelle) ?></span>
                        <svg class="w-5 h-5 text-dsi-400 ml-auto opacity-0 group-hover:opacity-100 transition" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                    </li>
                    <?php endforeach; ?>
                </ul>
                <div class="mt-8 pt-6 border-t border-white/10 grid grid-cols-2 gap-4">
                    <div class="bg-emerald-500/10 border border-emerald-400/20 rounded-xl px-4 py-3">
                        <p class="text-emerald-300 font-bold text-sm flex items-center gap-2">
                            <span class="w-2 h-2 bg-emerald-400 rounded-full animate-pulse"></span>
                            Support 24/7
                        </p>
                        <p class="text-emerald-200/60 text-xs mt-1">Assistance permanente</p>
                    </div>
                    <div class="bg-dsi-500/10 border border-dsi-400/20 rounded-xl px-4 py-3">
                        <p class="text-dsi-300 font-bold text-sm">Réponse &lt; 24h</p>
                        <p class="text-dsi-200/60 text-xs mt-1">Sous 24h ouvrées</p>
                    </div>
                </div>
            </div>
            <!-- Carte disponibilité (sous le cadre, en flux normal) -->
            <div class="mt-6 bg-white rounded-2xl shadow-2xl p-5 flex items-center gap-4 animate-flottement max-w-sm">
                <div class="w-12 h-12 bg-emerald-100 rounded-xl flex items-center justify-center shrink-0">
                    <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 1 1-18 0 9 9 0 0 1 18 0z"/></svg>
                </div>
                <div>
                    <p class="font-bold text-slate-900">99,9 % disponibilité</p>
                    <p class="text-sm text-slate-500">Infrastructure supervisée</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Section services (aperçu) -->
<section class="py-24 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-2xl mx-auto mb-16 reveal">
            <span class="text-dsi-600 font-semibold text-sm uppercase tracking-widest">Nos services</span>
            <h2 class="mt-3 text-3xl sm:text-4xl font-extrabold text-slate-900">Une expertise complète à votre service</h2>
            <p class="mt-4 text-slate-500 leading-relaxed">De la stratégie à l'exécution, nous couvrons l'ensemble de vos besoins informatiques.</p>
        </div>

        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-8">
            <?php foreach (array_slice($services, 0, 6) as $service): ?>
            <article class="group relative bg-slate-50 rounded-2xl p-8 border border-slate-100 hover:border-dsi-200 hover:shadow-xl hover:shadow-dsi-100 hover:-translate-y-1 transition-all duration-300 reveal">
                <div class="w-14 h-14 rounded-xl bg-gradient-to-br from-dsi-600 to-dsi-500 text-white flex items-center justify-center shadow-lg shadow-dsi-200 mb-6 group-hover:scale-110 transition-transform">
                    <?= icone_svg($service['icone']) ?>
                </div>
                <h3 class="text-xl font-bold text-slate-900 mb-3"><?= e($service['titre']) ?></h3>
                <p class="text-slate-500 leading-relaxed"><?= e($service['description']) ?></p>
                <a href="services.php#service-<?= (int) $service['id'] ?>" class="mt-5 inline-flex items-center gap-1.5 text-dsi-600 font-semibold text-sm group-hover:gap-3 transition-all">
                    En savoir plus
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5-5 5M6 12h12"/></svg>
                </a>
            </article>
            <?php endforeach; ?>
        </div>

        <div class="text-center mt-12 reveal">
            <a href="services.php" class="inline-flex items-center gap-2 border-2 border-dsi-600 text-dsi-600 hover:bg-dsi-600 hover:text-white font-semibold px-8 py-3.5 rounded-full transition">
                Voir tous nos services
            </a>
        </div>
    </div>
</section>

<!-- Section à propos -->
<section id="apropos" class="py-24 bg-slate-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 grid lg:grid-cols-2 gap-16 items-center">
        <div class="relative reveal">
            <div class="grid grid-cols-2 gap-5">
                <?php
                $chiffres = [
                    ['120+', 'Projets livrés', 'dsi'],
                    ['98%', 'Clients satisfaits', 'emerald'],
                    ['24/7', 'Support disponible', 'indigo'],
                    ['15+', 'Ans d\'expérience', 'amber'],
                ];
                foreach ($chiffres as [$valeur, $libelle, $couleur]): ?>
                <div class="bg-white rounded-2xl p-7 shadow-sm border border-slate-100 hover:shadow-xl hover:-translate-y-1 transition-all">
                    <p class="text-4xl font-extrabold text-<?= $couleur ?>-600"><?= e($valeur) ?></p>
                    <p class="text-sm text-slate-500 mt-2 font-medium"><?= e($libelle) ?></p>
                </div>
                <?php endforeach; ?>
            </div>
            <div class="mt-5 bg-gradient-to-r from-dsi-700 to-dsi-500 rounded-2xl p-6 text-white flex items-center gap-5 shadow-lg shadow-dsi-200">
                <span class="w-12 h-12 bg-white/15 rounded-xl flex items-center justify-center shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 15a7 7 0 1 0 0-14 7 7 0 0 0 0 14zM8.2 13.9L7 23l5-3 5 3-1.2-9.1"/></svg>
                </span>
                <div>
                    <p class="font-bold">Équipe certifiée</p>
                    <p class="text-sm text-dsi-100">Experts multi-technologies à votre service</p>
                </div>
            </div>
        </div>
        <div class="reveal">
            <span class="text-dsi-600 font-semibold text-sm uppercase tracking-widest">À propos de nous</span>
            <h2 class="mt-3 text-3xl sm:text-4xl font-extrabold text-slate-900">Votre partenaire technologique de confiance</h2>
            <p class="mt-6 text-slate-500 leading-relaxed">
                Notre équipe d'experts accompagne entreprises et organisations dans la maîtrise
                de leur système d'information. Nous croyons que la technologie doit être un
                levier de croissance, jamais une contrainte.
            </p>
            <ul class="mt-8 space-y-4">
                <?php
                $atouts = [
                    'Approche personnalisée adaptée à votre métier',
                    'Équipe certifiée et expertise multi-technologies',
                    'Transparence totale sur les délais et les coûts',
                    'Accompagnement de bout en bout de vos projets',
                ];
                foreach ($atouts as $atout): ?>
                <li class="flex items-start gap-3">
                    <span class="w-6 h-6 bg-emerald-100 rounded-full flex items-center justify-center shrink-0 mt-0.5">
                        <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                    </span>
                    <span class="text-slate-600"><?= e($atout) ?></span>
                </li>
                <?php endforeach; ?>
            </ul>
            <a href="contact.php" class="mt-10 inline-flex items-center gap-2 bg-dsi-600 hover:bg-dsi-500 text-white font-semibold px-8 py-4 rounded-full transition shadow-lg shadow-dsi-200">
                Parlons de votre projet
            </a>
        </div>
    </div>
</section>

<!-- Appel à l'action -->
<section class="py-20 bg-gradient-to-r from-dsi-700 via-dsi-600 to-dsi-500 relative overflow-hidden">
    <div class="absolute inset-0 bg-grid opacity-10"></div>
    <div class="relative max-w-4xl mx-auto px-4 text-center reveal">
        <h2 class="text-3xl sm:text-4xl font-extrabold text-white">Prêt à digitaliser votre entreprise ?</h2>
        <p class="mt-4 text-dsi-100 text-lg">Contactez-nous dès aujourd'hui pour un devis gratuit et sans engagement.</p>
        <a href="contact.php" class="mt-8 inline-flex items-center gap-2 bg-white text-dsi-700 hover:bg-dsi-50 font-bold px-10 py-4 rounded-full transition shadow-xl hover:-translate-y-0.5">
            Nous contacter
        </a>
    </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
