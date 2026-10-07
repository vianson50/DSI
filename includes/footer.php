<!-- Pied de page -->
<footer class="bg-dsi-950 text-slate-300">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
        <div class="grid gap-10 md:grid-cols-4">
            <div class="md:col-span-2">
                <div class="flex items-center gap-3 mb-4">
                    <img src="assets/img/logo.png" alt="Logo" class="h-14 w-auto">
                </div>
                <p class="max-w-md leading-relaxed text-slate-400">
                    Votre partenaire de confiance pour tous vos besoins informatiques :
                    développement, réseaux, sécurité et support technique. Nous transformons
                    la technologie en avantage compétitif pour votre entreprise.
                </p>
            </div>
            <div>
                <h3 class="text-white font-semibold mb-4">Navigation</h3>
                <ul class="space-y-2 text-sm">
                    <li><a href="index.php" class="hover:text-dsi-300 transition">Accueil</a></li>
                    <li><a href="services.php" class="hover:text-dsi-300 transition">Nos services</a></li>
                    <li><a href="boutique.php" class="hover:text-dsi-300 transition">Vente d'ordinateurs</a></li>
                    <li><a href="index.php#apropos" class="hover:text-dsi-300 transition">À propos</a></li>
                    <li><a href="contact.php" class="hover:text-dsi-300 transition">Contact</a></li>
                </ul>
            </div>
            <div>
                <h3 class="text-white font-semibold mb-4">Contact</h3>
                <ul class="space-y-3 text-sm">
                    <li class="flex items-center gap-3">
                        <svg class="w-5 h-5 text-dsi-400 shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 8l9 6 9-6M3 6h18v12H3V6z"/></svg>
                        <?= e(SITE_EMAIL) ?>
                    </li>
                    <li class="flex items-center gap-3">
                        <svg class="w-5 h-5 text-dsi-400 shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 5a2 2 0 0 1 2-2h2l2 5-2 1a11 11 0 0 0 5 5l1-2 5 2v2a2 2 0 0 1-2 2A16 16 0 0 1 3 5z"/></svg>
                        <a href="tel:<?= e(SITE_TELEPHONE) ?>" class="hover:text-dsi-300 transition"><?= e(SITE_TELEPHONE) ?></a>
                    </li>
                    <li class="flex items-center gap-3">
                        <svg class="w-5 h-5 text-emerald-400 shrink-0" fill="currentColor" viewBox="0 0 24 24"><path d="M12.04 2c-5.46 0-9.91 4.45-9.91 9.91 0 1.75.46 3.45 1.32 4.95L2.05 22l5.25-1.38a9.87 9.87 0 0 0 4.74 1.21h.01c5.46 0 9.9-4.45 9.9-9.91 0-2.65-1.03-5.14-2.9-7.01A9.82 9.82 0 0 0 12.04 2zm0 18.03h-.01a8.2 8.2 0 0 1-4.19-1.15l-.3-.18-3.12.82.83-3.04-.2-.31a8.2 8.2 0 0 1-1.26-4.38c0-4.54 3.7-8.24 8.25-8.24 2.2 0 4.27.86 5.82 2.42a8.18 8.18 0 0 1 2.41 5.83c0 4.54-3.7 8.23-8.23 8.23zm5.22-6.09c-.25-.13-1.47-.72-1.69-.81-.23-.08-.39-.12-.56.13-.16.24-.64.8-.78.97-.14.16-.29.18-.54.06-.25-.13-1.05-.39-2-1.23-.73-.66-1.23-1.47-1.38-1.72-.14-.24-.01-.38.11-.5.11-.11.25-.29.37-.43.12-.14.16-.25.25-.41.08-.17.04-.31-.02-.43-.06-.13-.56-1.34-.76-1.84-.2-.48-.41-.42-.56-.43h-.48c-.17 0-.43.06-.66.31-.22.24-.86.85-.86 2.07 0 1.22.89 2.4 1.01 2.56.12.17 1.75 2.67 4.23 3.74.59.26 1.05.41 1.41.52.6.19 1.13.16 1.56.1.48-.07 1.47-.6 1.67-1.18.21-.58.21-1.07.15-1.18-.06-.1-.23-.16-.48-.29z"/></svg>
                        <a href="<?= e(WHATSAPP_LIEN) ?>" target="_blank" rel="noopener" class="hover:text-emerald-300 transition">WhatsApp : <?= e(SITE_WHATSAPP) ?></a>
                    </li>
                    <li class="flex items-center gap-3">
                        <svg class="w-5 h-5 text-dsi-400 shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a2 2 0 0 1-2.827 0l-4.244-4.243a8 8 0 1 1 11.314 0z"/><circle cx="12" cy="11" r="2.5"/></svg>
                        <a href="<?= e(SITE_MAPS) ?>" target="_blank" rel="noopener" class="hover:text-dsi-300 transition">Nous trouver</a>
                    </li>
                </ul>
            </div>
        </div>
        <div class="mt-12 pt-8 border-t border-white/10 flex flex-col sm:flex-row items-center justify-between gap-4 text-sm text-slate-500">
            <p>© <?= date('Y') ?> — Tous droits réservés. Toute reproduction, même partielle, du contenu de ce site est interdite sans autorisation écrite préalable.</p>
            <a href="admin/login.php" class="hover:text-slate-300 transition">Espace administrateur</a>
        </div>
    </div>
</footer>

<!-- Bandeau de consentement cookies -->
<div id="bandeau-cookies" class="fixed bottom-0 inset-x-0 z-[60] hidden">
    <div class="m-4 sm:m-6 max-w-3xl mx-auto bg-white rounded-2xl shadow-2xl border border-slate-200 p-6 flex flex-col sm:flex-row items-start sm:items-center gap-5">
        <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-500 flex items-center justify-center shrink-0">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><circle cx="12" cy="12" r="9"/><circle cx="9" cy="10" r="1" fill="currentColor" stroke="none"/><circle cx="14" cy="8" r="1" fill="currentColor" stroke="none"/><circle cx="15" cy="13" r="1" fill="currentColor" stroke="none"/><circle cx="10" cy="15" r="1" fill="currentColor" stroke="none"/><path stroke-linecap="round" d="M12 3a9 9 0 0 1 9 9c0 .7-.1 1.4-.3 2-.5 1.7-2.1 2.9-3.9 2.9h-2.6a3 3 0 0 0-3 3c0 .5.1 1 .4 1.5A9 9 0 0 1 12 3z"/></svg>
        </div>
        <div class="flex-1">
            <p class="font-bold text-slate-900">Nous utilisons des cookies 🍪</p>
            <p class="text-sm text-slate-500 mt-1 leading-relaxed">
                Ce site utilise des cookies pour améliorer votre expérience et mémoriser vos préférences.
                En cliquant sur « Accepter », vous consentez à leur utilisation.
                <button type="button" id="btn-en-savoir-plus" class="text-dsi-600 font-semibold hover:underline">En savoir plus</button>
            </p>
        </div>
        <div class="flex gap-3 shrink-0 w-full sm:w-auto">
            <button type="button" id="btn-refuser-cookies"
                    class="flex-1 sm:flex-none px-5 py-2.5 rounded-xl border border-slate-200 text-slate-500 font-semibold text-sm hover:bg-slate-50 transition">
                Refuser
            </button>
            <button type="button" id="btn-accepter-cookies"
                    class="flex-1 sm:flex-none px-6 py-2.5 rounded-xl bg-dsi-600 hover:bg-dsi-500 text-white font-semibold text-sm transition shadow-lg shadow-dsi-200">
                Accepter
            </button>
        </div>
    </div>
</div>

<!-- Fenêtre d'information sur les cookies -->
<div id="modal-cookies" class="fixed inset-0 z-[70] hidden">
    <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm" id="fond-modal-cookies"></div>
    <div class="relative max-w-lg mx-auto mt-24 bg-white rounded-2xl shadow-2xl p-8">
        <h3 class="text-xl font-bold text-slate-900">Politique des cookies</h3>
        <div class="mt-4 text-sm text-slate-600 leading-relaxed space-y-3">
            <p>Un cookie est un petit fichier texte stocké sur votre appareil lors de la visite d'un site web.</p>
            <p><strong class="text-slate-800">Cookies utilisés sur ce site :</strong></p>
            <ul class="list-disc pl-5 space-y-1.5">
                <li><strong>Préférences (essentiels)</strong> : mémorisent votre choix de consentement afin de ne plus afficher ce bandeau.</li>
                <li><strong>Session (administration)</strong> : nécessaires à la connexion sécurisée de l'espace administrateur.</li>
            </ul>
            <p>Vous pouvez refuser les cookies non essentiels sans affecter votre navigation. Vous pouvez également les supprimer à tout moment depuis les réglages de votre navigateur.</p>
        </div>
        <button type="button" id="btn-fermer-modal"
                class="mt-6 w-full bg-dsi-600 hover:bg-dsi-500 text-white font-semibold py-3 rounded-xl transition">
            Fermer
        </button>
    </div>
</div>

<script src="assets/js/main.js"></script>
</body>
</html>
