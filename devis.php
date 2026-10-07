<?php
$titre_page = 'Demander un devis';
require_once __DIR__ . '/includes/functions.php';

$erreurs = [];
$succes = false;
$services_liste = get_services();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom         = trim($_POST['nom'] ?? '');
    $telephone   = trim($_POST['telephone'] ?? '');
    $email       = trim($_POST['email'] ?? '');
    $service     = trim($_POST['service'] ?? '');
    $description = trim($_POST['description'] ?? '');

    if ($nom === '' || mb_strlen($nom) > 100) {
        $erreurs['nom'] = 'Veuillez indiquer votre nom.';
    }
    if ($telephone === '' || mb_strlen($telephone) > 30) {
        $erreurs['telephone'] = 'Veuillez indiquer votre téléphone (pour vous envoyer le devis sur WhatsApp).';
    }
    if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $erreurs['email'] = 'Adresse e-mail invalide.';
    }
    if ($service === '') {
        $erreurs['service'] = 'Veuillez choisir un service.';
    }
    if ($description === '' || mb_strlen($description) > 3000) {
        $erreurs['description'] = 'Veuillez décrire votre besoin.';
    }

    if (empty($erreurs)) {
        db()->prepare('INSERT INTO demandes_devis (nom, telephone, email, service, description) VALUES (?, ?, ?, ?, ?)')
            ->execute([$nom, $telephone, $email ?: null, $service, $description]);
        $succes = true;
    }
}

require __DIR__ . '/includes/header.php';
?>

<!-- En-tête de page -->
<section class="pt-40 pb-20 bg-dsi-950 relative overflow-hidden">
    <div class="absolute -bottom-24 -right-24 w-96 h-96 bg-dsi-600/30 rounded-full blur-3xl"></div>
    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <span class="text-dsi-300 font-semibold text-sm uppercase tracking-widest">Gratuit &amp; sans engagement</span>
        <h1 class="mt-3 text-4xl sm:text-5xl font-extrabold text-white">Demander un devis</h1>
        <p class="mt-5 text-slate-300 max-w-2xl mx-auto leading-relaxed">
            Décrivez votre besoin en quelques clics. Nous vous envoyons un devis
            détaillé sur WhatsApp dans les plus brefs délais.
        </p>
    </div>
</section>

<!-- Formulaire -->
<section class="py-24 bg-slate-50">
    <div class="max-w-3xl mx-auto px-4 sm:px-6">

        <?php if ($succes): ?>
        <div class="mb-10 bg-emerald-50 border border-emerald-200 rounded-2xl p-8 text-center" id="alerte-succes">
            <div class="w-16 h-16 bg-emerald-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-emerald-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
            </div>
            <h2 class="text-2xl font-extrabold text-emerald-800">Demande envoyée !</h2>
            <p class="text-emerald-700 mt-3 leading-relaxed">
                Merci pour votre demande. Nous allons préparer votre devis et vous
                l'envoyer sur WhatsApp très rapidement.
            </p>
            <div class="mt-6 flex flex-wrap justify-center gap-4">
                <a href="<?= e(WHATSAPP_LIEN) ?>" target="_blank" rel="noopener"
                   class="inline-flex items-center gap-2 bg-emerald-500 hover:bg-emerald-600 text-white font-semibold px-6 py-3 rounded-xl transition shadow-lg">
                    Nous écrire sur WhatsApp
                </a>
                <a href="index.php" class="inline-flex items-center gap-2 border border-slate-200 bg-white text-slate-600 hover:bg-slate-50 font-semibold px-6 py-3 rounded-xl transition">
                    Retour à l'accueil
                </a>
            </div>
        </div>
        <?php else: ?>

        <form method="post" action="devis.php" class="bg-white rounded-2xl p-8 sm:p-10 shadow-sm border border-slate-100 reveal" novalidate>
            <div class="grid sm:grid-cols-2 gap-6">
                <div>
                    <label for="nom" class="block text-sm font-semibold text-slate-700 mb-2">Nom complet <span class="text-red-500">*</span></label>
                    <input type="text" id="nom" name="nom" required maxlength="100"
                           value="<?= e($_POST['nom'] ?? '') ?>"
                           class="w-full rounded-xl border border-slate-200 px-4 py-3 focus:outline-none focus:ring-2 focus:ring-dsi-500 transition <?= isset($erreurs['nom']) ? 'border-red-400' : '' ?>"
                           placeholder="Votre nom">
                    <?php if (isset($erreurs['nom'])): ?><p class="text-red-500 text-sm mt-1.5"><?= e($erreurs['nom']) ?></p><?php endif; ?>
                </div>
                <div>
                    <label for="telephone" class="block text-sm font-semibold text-slate-700 mb-2">Téléphone / WhatsApp <span class="text-red-500">*</span></label>
                    <input type="tel" id="telephone" name="telephone" required maxlength="30"
                           value="<?= e($_POST['telephone'] ?? '') ?>"
                           class="w-full rounded-xl border border-slate-200 px-4 py-3 focus:outline-none focus:ring-2 focus:ring-dsi-500 transition <?= isset($erreurs['telephone']) ? 'border-red-400' : '' ?>"
                           placeholder="Ex : 07 57 75 65 27">
                    <?php if (isset($erreurs['telephone'])): ?><p class="text-red-500 text-sm mt-1.5"><?= e($erreurs['telephone']) ?></p><?php endif; ?>
                </div>
                <div>
                    <label for="email" class="block text-sm font-semibold text-slate-700 mb-2">E-mail <span class="text-slate-400 font-normal">(optionnel)</span></label>
                    <input type="email" id="email" name="email" maxlength="150"
                           value="<?= e($_POST['email'] ?? '') ?>"
                           class="w-full rounded-xl border border-slate-200 px-4 py-3 focus:outline-none focus:ring-2 focus:ring-dsi-500 transition"
                           placeholder="vous@exemple.com">
                    <?php if (isset($erreurs['email'])): ?><p class="text-red-500 text-sm mt-1.5"><?= e($erreurs['email']) ?></p><?php endif; ?>
                </div>
                <div>
                    <label for="service" class="block text-sm font-semibold text-slate-700 mb-2">Service souhaité <span class="text-red-500">*</span></label>
                    <select id="service" name="service" required
                            class="w-full rounded-xl border border-slate-200 px-4 py-3 focus:outline-none focus:ring-2 focus:ring-dsi-500 transition bg-white <?= isset($erreurs['service']) ? 'border-red-400' : '' ?>">
                        <option value="">— Choisir un service —</option>
                        <?php foreach ($services_liste as $srv): ?>
                        <option value="<?= e($srv['titre']) ?>" <?= ($_POST['service'] ?? '') === $srv['titre'] ? 'selected' : '' ?>>
                            <?= e($srv['titre']) ?>
                        </option>
                        <?php endforeach; ?>
                        <option value="Autre demande">Autre demande</option>
                    </select>
                    <?php if (isset($erreurs['service'])): ?><p class="text-red-500 text-sm mt-1.5"><?= e($erreurs['service']) ?></p><?php endif; ?>
                </div>
                <div class="sm:col-span-2">
                    <label for="description" class="block text-sm font-semibold text-slate-700 mb-2">Décrivez votre besoin <span class="text-red-500">*</span></label>
                    <textarea id="description" name="description" required rows="5" maxlength="3000"
                              class="w-full rounded-xl border border-slate-200 px-4 py-3 focus:outline-none focus:ring-2 focus:ring-dsi-500 transition resize-y <?= isset($erreurs['description']) ? 'border-red-400' : '' ?>"
                              placeholder="Ex : installation de 4 caméras de surveillance dans une boutique à …"><?= e($_POST['description'] ?? '') ?></textarea>
                    <?php if (isset($erreurs['description'])): ?><p class="text-red-500 text-sm mt-1.5"><?= e($erreurs['description']) ?></p><?php endif; ?>
                </div>
            </div>

            <button type="submit"
                    class="mt-8 w-full sm:w-auto bg-dsi-600 hover:bg-dsi-500 text-white font-semibold px-10 py-4 rounded-xl transition shadow-lg shadow-dsi-200">
                Envoyer ma demande de devis
            </button>
        </form>
        <?php endif; ?>
    </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
