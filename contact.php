<?php
$titre_page = 'Contact';
require_once __DIR__ . '/includes/functions.php';

$erreurs = [];
$succes = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom       = trim($_POST['nom'] ?? '');
    $email     = trim($_POST['email'] ?? '');
    $telephone = trim($_POST['telephone'] ?? '');
    $sujet     = trim($_POST['sujet'] ?? '');
    $message   = trim($_POST['message'] ?? '');

    // Validation serveur
    if ($nom === '' || mb_strlen($nom) > 100) {
        $erreurs['nom'] = 'Veuillez indiquer votre nom (100 caractères max).';
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $erreurs['email'] = 'Adresse e-mail invalide.';
    }
    if ($telephone !== '' && mb_strlen($telephone) > 30) {
        $erreurs['telephone'] = 'Numéro de téléphone trop long.';
    }
    if ($sujet === '' || mb_strlen($sujet) > 200) {
        $erreurs['sujet'] = 'Veuillez indiquer un sujet (200 caractères max).';
    }
    if ($message === '' || mb_strlen($message) > 5000) {
        $erreurs['message'] = 'Veuillez écrire votre message (5000 caractères max).';
    }

    if (empty($erreurs)) {
        $stmt = db()->prepare(
            'INSERT INTO messages (nom, email, telephone, sujet, message) VALUES (?, ?, ?, ?, ?)'
        );
        $stmt->execute([$nom, $email, $telephone ?: null, $sujet, $message]);
        $succes = true;
    }
}

require __DIR__ . '/includes/header.php';
?>

<!-- En-tête de page -->
<section class="pt-40 pb-20 bg-dsi-950 relative overflow-hidden">
    <div class="absolute -bottom-24 -left-24 w-96 h-96 bg-dsi-600/30 rounded-full blur-3xl"></div>
    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <span class="text-dsi-300 font-semibold text-sm uppercase tracking-widest">Parlons-en</span>
        <h1 class="mt-3 text-4xl sm:text-5xl font-extrabold text-white">Contactez-nous</h1>
        <p class="mt-5 text-slate-300 max-w-2xl mx-auto leading-relaxed">
            Une question, un projet, un besoin d'information ? Écrivez-nous,
            nous vous répondrons dans les plus brefs délais.
        </p>
    </div>
</section>

<!-- Formulaire + coordonnées -->
<section class="py-24 bg-slate-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 grid lg:grid-cols-5 gap-12">

        <!-- Coordonnées -->
        <aside class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-2xl p-8 shadow-sm border border-slate-100 reveal">
                <h2 class="text-xl font-bold text-slate-900 mb-6">Nos coordonnées</h2>
                <ul class="space-y-5">
                    <li class="flex items-start gap-4">
                        <span class="w-11 h-11 rounded-xl bg-dsi-50 text-dsi-600 flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 8l9 6 9-6M3 6h18v12H3V6z"/></svg>
                        </span>
                        <div>
                            <p class="font-semibold text-slate-900">E-mail</p>
                            <p class="text-slate-500"><?= e(SITE_EMAIL) ?></p>
                        </div>
                    </li>
                    <li class="flex items-start gap-4">
                        <span class="w-11 h-11 rounded-xl bg-dsi-50 text-dsi-600 flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 5a2 2 0 0 1 2-2h2l2 5-2 1a11 11 0 0 0 5 5l1-2 5 2v2a2 2 0 0 1-2 2A16 16 0 0 1 3 5z"/></svg>
                        </span>
                        <div>
                            <p class="font-semibold text-slate-900">Téléphone</p>
                            <a href="tel:<?= e(SITE_TELEPHONE) ?>" class="text-slate-500 hover:text-dsi-600"><?= e(SITE_TELEPHONE) ?></a>
                        </div>
                    </li>
                    <li class="flex items-start gap-4">
                        <span class="w-11 h-11 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12.04 2c-5.46 0-9.91 4.45-9.91 9.91 0 1.75.46 3.45 1.32 4.95L2.05 22l5.25-1.38a9.87 9.87 0 0 0 4.74 1.21h.01c5.46 0 9.9-4.45 9.9-9.91 0-2.65-1.03-5.14-2.9-7.01A9.82 9.82 0 0 0 12.04 2zm0 18.03h-.01a8.2 8.2 0 0 1-4.19-1.15l-.3-.18-3.12.82.83-3.04-.2-.31a8.2 8.2 0 0 1-1.26-4.38c0-4.54 3.7-8.24 8.25-8.24 2.2 0 4.27.86 5.82 2.42a8.18 8.18 0 0 1 2.41 5.83c0 4.54-3.7 8.23-8.23 8.23zm5.22-6.09c-.25-.13-1.47-.72-1.69-.81-.23-.08-.39-.12-.56.13-.16.24-.64.8-.78.97-.14.16-.29.18-.54.06-.25-.13-1.05-.39-2-1.23-.73-.66-1.23-1.47-1.38-1.72-.14-.24-.01-.38.11-.5.11-.11.25-.29.37-.43.12-.14.16-.25.25-.41.08-.17.04-.31-.02-.43-.06-.13-.56-1.34-.76-1.84-.2-.48-.41-.42-.56-.43h-.48c-.17 0-.43.06-.66.31-.22.24-.86.85-.86 2.07 0 1.22.89 2.4 1.01 2.56.12.17 1.75 2.67 4.23 3.74.59.26 1.05.41 1.41.52.6.19 1.13.16 1.56.1.48-.07 1.47-.6 1.67-1.18.21-.58.21-1.07.15-1.18-.06-.1-.23-.16-.48-.29z"/></svg>
                        </span>
                        <div>
                            <p class="font-semibold text-slate-900">WhatsApp</p>
                            <a href="<?= e(WHATSAPP_LIEN) ?>" target="_blank" rel="noopener" class="text-slate-500 hover:text-emerald-600"><?= e(SITE_WHATSAPP) ?></a>
                        </div>
                    </li>
                    <li class="flex items-start gap-4">
                        <span class="w-11 h-11 rounded-xl bg-dsi-50 text-dsi-600 flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a2 2 0 0 1-2.827 0l-4.244-4.243a8 8 0 1 1 11.314 0z"/><circle cx="12" cy="11" r="2.5"/></svg>
                        </span>
                        <div>
                            <p class="font-semibold text-slate-900">Localisation</p>
                            <a href="<?= e(SITE_MAPS) ?>" target="_blank" rel="noopener" class="text-dsi-600 hover:underline">
                                Voir sur Google Maps
                            </a>
                        </div>
                    </li>
                    <li class="flex items-start gap-4">
                        <span class="w-11 h-11 rounded-xl bg-dsi-50 text-dsi-600 flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><circle cx="12" cy="12" r="9"/><path stroke-linecap="round" d="M12 7v5l3 3"/></svg>
                        </span>
                        <div>
                            <p class="font-semibold text-slate-900">Horaires</p>
                            <p class="text-slate-500">Lun – Ven : 9h00 – 18h00</p>
                        </div>
                    </li>
                </ul>
            </div>
            <div class="bg-gradient-to-br from-emerald-600 to-emerald-500 rounded-2xl p-8 text-white reveal">
                <h3 class="font-bold text-lg">Écrivez-nous sur WhatsApp</h3>
                <p class="mt-2 text-emerald-100 text-sm leading-relaxed">
                    Réponse rapide et directe sur WhatsApp : <?= e(SITE_WHATSAPP) ?>.
                </p>
                <a href="<?= e(WHATSAPP_LIEN) ?>" target="_blank" rel="noopener"
                   class="mt-5 inline-flex items-center gap-2 bg-white text-emerald-600 hover:bg-emerald-50 font-bold px-6 py-3 rounded-xl transition shadow-lg">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12.04 2c-5.46 0-9.91 4.45-9.91 9.91 0 1.75.46 3.45 1.32 4.95L2.05 22l5.25-1.38a9.87 9.87 0 0 0 4.74 1.21h.01c5.46 0 9.9-4.45 9.9-9.91 0-2.65-1.03-5.14-2.9-7.01A9.82 9.82 0 0 0 12.04 2zm0 18.03h-.01a8.2 8.2 0 0 1-4.19-1.15l-.3-.18-3.12.82.83-3.04-.2-.31a8.2 8.2 0 0 1-1.26-4.38c0-4.54 3.7-8.24 8.25-8.24 2.2 0 4.27.86 5.82 2.42a8.18 8.18 0 0 1 2.41 5.83c0 4.54-3.7 8.23-8.23 8.23zm5.22-6.09c-.25-.13-1.47-.72-1.69-.81-.23-.08-.39-.12-.56.13-.16.24-.64.8-.78.97-.14.16-.29.18-.54.06-.25-.13-1.05-.39-2-1.23-.73-.66-1.23-1.47-1.38-1.72-.14-.24-.01-.38.11-.5.11-.11.25-.29.37-.43.12-.14.16-.25.25-.41.08-.17.04-.31-.02-.43-.06-.13-.56-1.34-.76-1.84-.2-.48-.41-.42-.56-.43h-.48c-.17 0-.43.06-.66.31-.22.24-.86.85-.86 2.07 0 1.22.89 2.4 1.01 2.56.12.17 1.75 2.67 4.23 3.74.59.26 1.05.41 1.41.52.6.19 1.13.16 1.56.1.48-.07 1.47-.6 1.67-1.18.21-.58.21-1.07.15-1.18-.06-.1-.23-.16-.48-.29z"/></svg>
                    Discuter sur WhatsApp
                </a>
            </div>
        </aside>

        <!-- Formulaire -->
        <div class="lg:col-span-3 bg-white rounded-2xl p-8 sm:p-10 shadow-sm border border-slate-100 reveal">
            <?php if ($succes): ?>
            <div class="mb-8 bg-emerald-50 border border-emerald-200 rounded-xl p-5 flex items-start gap-3" id="alerte-succes">
                <svg class="w-6 h-6 text-emerald-600 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 1 1-18 0 9 9 0 0 1 18 0z"/></svg>
                <div>
                    <p class="font-semibold text-emerald-800">Message envoyé avec succès !</p>
                    <p class="text-emerald-700 text-sm mt-1">Merci pour votre message. Nous vous répondrons très rapidement.</p>
                </div>
            </div>
            <?php endif; ?>

            <h2 class="text-2xl font-bold text-slate-900 mb-8">Envoyez-nous un message</h2>

            <form method="post" action="contact.php" id="formulaire-contact" novalidate>
                <div class="grid sm:grid-cols-2 gap-6">
                    <div>
                        <label for="nom" class="block text-sm font-semibold text-slate-700 mb-2">Nom complet <span class="text-red-500">*</span></label>
                        <input type="text" id="nom" name="nom" required maxlength="100"
                               value="<?= e($_POST['nom'] ?? '') ?>"
                               class="champ-formulaire w-full rounded-xl border-slate-200 border px-4 py-3 focus:outline-none focus:ring-2 focus:ring-dsi-500 focus:border-dsi-500 transition <?= isset($erreurs['nom']) ? 'border-red-400' : '' ?>"
                               placeholder="Votre nom">
                        <?php if (isset($erreurs['nom'])): ?><p class="text-red-500 text-sm mt-1.5"><?= e($erreurs['nom']) ?></p><?php endif; ?>
                    </div>
                    <div>
                        <label for="email" class="block text-sm font-semibold text-slate-700 mb-2">E-mail <span class="text-red-500">*</span></label>
                        <input type="email" id="email" name="email" required maxlength="150"
                               value="<?= e($_POST['email'] ?? '') ?>"
                               class="champ-formulaire w-full rounded-xl border-slate-200 border px-4 py-3 focus:outline-none focus:ring-2 focus:ring-dsi-500 focus:border-dsi-500 transition <?= isset($erreurs['email']) ? 'border-red-400' : '' ?>"
                               placeholder="vous@exemple.com">
                        <?php if (isset($erreurs['email'])): ?><p class="text-red-500 text-sm mt-1.5"><?= e($erreurs['email']) ?></p><?php endif; ?>
                    </div>
                    <div>
                        <label for="telephone" class="block text-sm font-semibold text-slate-700 mb-2">Téléphone <span class="text-slate-400 font-normal">(optionnel)</span></label>
                        <input type="tel" id="telephone" name="telephone" maxlength="30"
                               value="<?= e($_POST['telephone'] ?? '') ?>"
                               class="champ-formulaire w-full rounded-xl border-slate-200 border px-4 py-3 focus:outline-none focus:ring-2 focus:ring-dsi-500 focus:border-dsi-500 transition"
                               placeholder="+212 6 00 00 00 00">
                    </div>
                    <div>
                        <label for="sujet" class="block text-sm font-semibold text-slate-700 mb-2">Sujet <span class="text-red-500">*</span></label>
                        <input type="text" id="sujet" name="sujet" required maxlength="200"
                               value="<?= e($_POST['sujet'] ?? '') ?>"
                               class="champ-formulaire w-full rounded-xl border-slate-200 border px-4 py-3 focus:outline-none focus:ring-2 focus:ring-dsi-500 focus:border-dsi-500 transition <?= isset($erreurs['sujet']) ? 'border-red-400' : '' ?>"
                               placeholder="Objet de votre message">
                        <?php if (isset($erreurs['sujet'])): ?><p class="text-red-500 text-sm mt-1.5"><?= e($erreurs['sujet']) ?></p><?php endif; ?>
                    </div>
                    <div class="sm:col-span-2">
                        <label for="message" class="block text-sm font-semibold text-slate-700 mb-2">Message <span class="text-red-500">*</span></label>
                        <textarea id="message" name="message" required rows="6" maxlength="5000"
                                  class="champ-formulaire w-full rounded-xl border-slate-200 border px-4 py-3 focus:outline-none focus:ring-2 focus:ring-dsi-500 focus:border-dsi-500 transition resize-y <?= isset($erreurs['message']) ? 'border-red-400' : '' ?>"
                                  placeholder="Décrivez votre besoin ou votre question…"><?= e($_POST['message'] ?? '') ?></textarea>
                        <?php if (isset($erreurs['message'])): ?><p class="text-red-500 text-sm mt-1.5"><?= e($erreurs['message']) ?></p><?php endif; ?>
                        <p class="text-slate-400 text-xs mt-1.5 text-right"><span id="compteur-caracteres">0</span> / 5000 caractères</p>
                    </div>
                </div>

                <!-- Protection anti-spam simple (honeypot) -->
                <div class="hidden" aria-hidden="true">
                    <label for="site-web">Ne pas remplir</label>
                    <input type="text" id="site-web" name="site_web" tabindex="-1" autocomplete="off">
                </div>

                <button type="submit" id="btn-envoyer"
                        class="mt-8 w-full sm:w-auto inline-flex items-center justify-center gap-2 bg-dsi-600 hover:bg-dsi-500 text-white font-semibold px-10 py-4 rounded-xl transition shadow-lg shadow-dsi-200 disabled:opacity-60 disabled:cursor-not-allowed">
                    <span id="texte-btn">Envoyer le message</span>
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                </button>
            </form>
        </div>
    </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
