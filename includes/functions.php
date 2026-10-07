<?php
/**
 * Fonctions utilitaires partagées.
 */
require_once __DIR__ . '/config.php';

/** Échapper une chaîne pour l'affichage HTML (protection XSS). */
function e(?string $valeur): string
{
    return htmlspecialchars((string) $valeur, ENT_QUOTES, 'UTF-8');
}

/** Générer / récupérer un jeton CSRF. */
function csrf_token(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/** Vérifier le jeton CSRF soumis. */
function csrf_valide(?string $jeton): bool
{
    return isset($_SESSION['csrf_token']) && $jeton !== null
        && hash_equals($_SESSION['csrf_token'], $jeton);
}

/** Récupérer les services actifs, triés par ordre d'affichage. */
function get_services(bool $tous = false): array
{
    $sql = 'SELECT * FROM services';
    if (!$tous) {
        $sql .= ' WHERE actif = 1';
    }
    $sql .= ' ORDER BY ordre_affichage ASC, id ASC';
    $stmt = db()->query($sql);
    return $stmt->fetchAll();
}

/** Récupérer un service par son identifiant. */
function get_service(int $id): ?array
{
    $stmt = db()->prepare('SELECT * FROM services WHERE id = ?');
    $stmt->execute([$id]);
    $service = $stmt->fetch();
    return $service ?: null;
}

/** Récupérer les messages de contact. */
function get_messages(bool $uniquement_non_lus = false): array
{
    $sql = 'SELECT * FROM messages';
    if ($uniquement_non_lus) {
        $sql .= ' WHERE lu = 0';
    }
    $sql .= ' ORDER BY cree_le DESC';
    return db()->query($sql)->fetchAll();
}

/** Nombre de messages non lus. */
function compter_messages_non_lus(): int
{
    return (int) db()->query('SELECT COUNT(*) FROM messages WHERE lu = 0')->fetchColumn();
}

/** Formater un prix (ex : 350000 -> 350 000 FCFA). */
function formater_prix(?string $prix, string $devise = 'FCFA'): string
{
    if ($prix === null || $prix === '') {
        return 'Prix à consulter';
    }
    return number_format((float) $prix, 0, ',', ' ') . ' ' . e($devise);
}

/** Récupérer les ordinateurs en vente. */
function get_ordinateurs(bool $tous = false): array
{
    $sql = 'SELECT * FROM ordinateurs';
    if (!$tous) {
        $sql .= ' WHERE actif = 1';
    }
    $sql .= ' ORDER BY ordre_affichage ASC, id ASC';
    return db()->query($sql)->fetchAll();
}

/** Récupérer un ordinateur par son identifiant. */
function get_ordinateur(int $id): ?array
{
    $stmt = db()->prepare('SELECT * FROM ordinateurs WHERE id = ?');
    $stmt->execute([$id]);
    $pc = $stmt->fetch();
    return $pc ?: null;
}

/** Générer le prochain numéro de devis (ex : DEV-2026-003). */
function generer_numero_devis(): string
{
    $annee = date('Y');
    $stmt = db()->prepare('SELECT COUNT(*) FROM devis WHERE numero LIKE ?');
    $stmt->execute(['DEV-' . $annee . '-%']);
    return sprintf('DEV-%s-%03d', $annee, (int) $stmt->fetchColumn() + 1);
}

/** Récupérer un devis avec ses lignes. */
function get_devis(int $id): ?array
{
    $stmt = db()->prepare('SELECT * FROM devis WHERE id = ?');
    $stmt->execute([$id]);
    $devis = $stmt->fetch();
    if (!$devis) return null;
    $lignes = db()->prepare('SELECT * FROM devis_lignes WHERE devis_id = ? ORDER BY id ASC');
    $lignes->execute([$id]);
    $devis['lignes'] = $lignes->fetchAll();
    return $devis;
}

/** Libellés et couleurs des statuts de devis. */
function statut_devis(string $statut): array
{
    $statuts = [
        'brouillon' => ['Brouillon', 'bg-slate-100 text-slate-600'],
        'envoye'    => ['Envoyé', 'bg-dsi-50 text-dsi-700'],
        'accepte'   => ['Accepté', 'bg-emerald-50 text-emerald-700'],
        'refuse'    => ['Refusé', 'bg-red-50 text-red-600'],
    ];
    return $statuts[$statut] ?? $statuts['brouillon'];
}

/** Nombre de demandes de devis non traitées. */
function compter_demandes_devis_non_traitees(): int
{
    return (int) db()->query('SELECT COUNT(*) FROM demandes_devis WHERE traitee = 0')->fetchColumn();
}

/** Cartes d'icônes SVG associées aux services. */
function icone_svg(string $nom): string
{
    $icones = [
        'code'      => '<path d="M16 18l6-6-6-6M8 6l-6 6 6 6"/>',
        'wrench'    => '<path d="M14.7 6.3a4.5 4.5 0 0 0-6 6L3 18l3 3 5.7-5.7a4.5 4.5 0 0 0 6-6L14 13l-3-3 3.7-3.7z"/>',
        'network'   => '<circle cx="12" cy="5" r="3"/><circle cx="5" cy="19" r="3"/><circle cx="19" cy="19" r="3"/><path d="M12 8v4m0 0l-5.5 4.5M12 12l5.5 4.5"/>',
        'shield'    => '<path d="M12 3l8 4v5c0 5-3.5 8-8 9-4.5-1-8-4-8-9V7l8-4z"/><path d="M9 12l2 2 4-4"/>',
        'lightbulb' => '<path d="M9 18h6M10 22h4M12 2a7 7 0 0 1 4 12.7c-.6.5-1 1.4-1 2.3h-6c0-.9-.4-1.8-1-2.3A7 7 0 0 1 12 2z"/>',
        'headset'   => '<path d="M4 13a8 8 0 0 1 16 0"/><rect x="2" y="13" width="4" height="7" rx="2"/><rect x="18" y="13" width="4" height="7" rx="2"/><path d="M20 20a4 4 0 0 1-4 3h-3"/>',
        'cloud'     => '<path d="M17.5 19a4.5 4.5 0 0 0 .4-9A7 7 0 0 0 4.3 12.5 3.5 3.5 0 0 0 6 19h11.5z"/>',
        'database'  => '<ellipse cx="12" cy="5" rx="8" ry="3"/><path d="M4 5v14c0 1.7 3.6 3 8 3s8-1.3 8-3V5"/><path d="M4 12c0 1.7 3.6 3 8 3s8-1.3 8-3"/>',
        'camera'    => '<path d="M15 10l6-3v10l-6-3"/><rect x="2" y="6" width="13" height="12" rx="2"/>',
        'laptop'    => '<rect x="4" y="5" width="16" height="11" rx="2"/><path d="M2 19h20"/>',
        'phone'     => '<rect x="7" y="2" width="10" height="20" rx="2"/><path d="M11 18h2"/>',
        'clipboard' => '<rect x="6" y="4" width="12" height="17" rx="2"/><path d="M9 4a3 3 0 0 1 6 0M9 11h6M9 15h4"/>',
        'disk'      => '<circle cx="12" cy="12" r="9"/><circle cx="12" cy="12" r="2.5"/><path d="M12 3v6.5M12 14.5V21"/>',
        'cpu'       => '<rect x="6" y="6" width="12" height="12" rx="2"/><rect x="10" y="10" width="4" height="4"/><path d="M9 3v3M15 3v3M9 18v3M15 18v3M3 9h3M3 15h3M18 9h3M18 15h3"/>',
        'printer'   => '<path d="M7 8V3h10v5"/><rect x="4" y="8" width="16" height="8" rx="2"/><path d="M7 13h10v8H7z"/>',
        'card'      => '<rect x="2" y="5" width="20" height="14" rx="2"/><path d="M2 10h20M6 15h5"/>',
    ];
    $chemin = $icones[$nom] ?? $icones['code'];
    return '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="w-7 h-7">' . $chemin . '</svg>';
}

/** Formater une date en français. */
function date_fr(string $date_sql): string
{
    $mois = [1 => 'janvier', 'février', 'mars', 'avril', 'mai', 'juin',
        'juillet', 'août', 'septembre', 'octobre', 'novembre', 'décembre'];
    $t = strtotime($date_sql);
    if (!$t) return $date_sql;
    return sprintf('%d %s %d à %02d:%02d', date('j', $t), $mois[(int) date('n', $t)], date('Y', $t), date('H', $t), date('i', $t));
}
