<?php
/**
 * Devis / Facture proforma au format A4 — design professionnel.
 * Ouvre la boîte de dialogue d'impression automatiquement (Enregistrer en PDF).
 */
require_once __DIR__ . '/garde.php';

$devis = get_devis((int) ($_GET['id'] ?? 0));
if (!$devis) {
    http_response_code(404);
    die('Devis introuvable.');
}

/* ---------- Calculs ---------- */
$total_ht = 0;
foreach ($devis['lignes'] as $ligne) {
    $total_ht += (float) $ligne['quantite'] * (float) $ligne['prix_unitaire'];
}
$tva_taux = 0.18; // TVA Côte d'Ivoire 18 % — mettre 0 pour ne pas l'afficher
$total_tva = $total_ht * $tva_taux;
$total_ttc = $total_ht + $total_tva;

/* ---------- Logo intégré en base64 (affichage garanti même à l'impression) ---------- */
$logo_base64 = 'data:image/png;base64,' . base64_encode(file_get_contents(__DIR__ . '/../assets/img/logo.png'));

/* ---------- Montant en lettres (français) ---------- */
function en_lettres(float $n): string
{
    $unites = ['zéro', 'un', 'deux', 'trois', 'quatre', 'cinq', 'six', 'sept', 'huit', 'neuf',
        'dix', 'onze', 'douze', 'treize', 'quatorze', 'quinze', 'seize', 'dix-sept', 'dix-huit', 'dix-neuf'];
    $dizaines = ['', '', 'vingt', 'trente', 'quarante', 'cinquante', 'soixante', 'soixante', 'quatre-vingt', 'quatre-vingt'];

    $centaines = null;
    $centaines = static function (int $c) use (&$centaines, $unites, $dizaines): string {
        if ($c === 0) return '';
        if ($c < 20) return $unites[$c];
        if ($c < 100) {
            $d = intdiv($c, 10);
            $u = $c % 10;
            $d = ($d === 7 || $d === 9) ? $d - 1 : $d;
            $reste = $c - $d * 10;
            $txt = $dizaines[$d + 1];
            if ($d === 8 && $reste === 0) $txt .= 's';
            if ($reste > 0) $txt .= ($reste === 1 && $d !== 8 ? '-et-' : '-') . $unites[$reste];
            return $txt;
        }
        $nbC = intdiv($c, 100);
        $reste = $c % 100;
        $txt = ($nbC > 1 ? $unites[$nbC] . '-cent' : 'cent');
        if ($reste > 0) $txt .= '-' . $centaines($reste);
        elseif ($nbC > 1) $txt .= 's';
        return $txt;
    };

    if ($n == 0) return 'zéro';
    $parts = [];
    $millions = (int) floor($n / 1000000);
    $milliers = (int) floor(($n % 1000000) / 1000);
    $reste = (int) $n % 1000;
    if ($millions > 0) $parts[] = ($millions > 1 ? $centaines($millions) . ' millions' : 'un million');
    if ($milliers > 0) $parts[] = ($milliers > 1 ? $centaines($milliers) . ' mille' : 'mille');
    if ($reste > 0) $parts[] = $centaines($reste);
    return implode(' ', $parts);
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Proforma <?= e($devis['numero']) ?> — <?= e($devis['client_nom']) ?></title>
    <style>
        @page { size: A4; margin: 0; }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Arial, sans-serif; color: #1e293b; background: #e2e8f0; }
        .page { width: 210mm; min-height: 297mm; margin: 0 auto; background: #fff; position: relative; padding-bottom: 22mm; }

        /* ===== Bandeau d'en-tête ===== */
        .bandeau {
            background: linear-gradient(135deg, #1e3a8a 0%, #2563eb 100%);
            color: #fff; padding: 10mm 15mm; display: flex;
            justify-content: space-between; align-items: center;
        }
        .bandeau img { height: 22mm; filter: drop-shadow(0 2px 4px rgba(0,0,0,.3)); }
        .bandeau .doc {
            text-align: right; border-right: none; padding-right: 0;
        }
        .bandeau .doc h1 { font-size: 20pt; letter-spacing: 4px; font-weight: 800; }
        .bandeau .doc .num {
            display: inline-block; margin-top: 2.5mm; background: rgba(255,255,255,.18);
            border: 1px solid rgba(255,255,255,.35); border-radius: 2mm;
            padding: 1.5mm 5mm; font-size: 11pt; font-weight: 600; font-family: 'Consolas', monospace;
        }
        .bandeau .doc .date { font-size: 9pt; margin-top: 2.5mm; opacity: .85; }

        /* ===== Bande tricolore décorative ===== */
        .bande { height: 2.5mm; background: linear-gradient(90deg, #f59e0b 33%, #3b82f6 33%, #3b82f6 66%, #10b981 66%); }

        /* ===== Corps ===== */
        .corps { padding: 8mm 15mm; }

        /* Blocs émetteur / client */
        .blocs { display: flex; gap: 8mm; }
        .bloc {
            flex: 1; border: 1px solid #e2e8f0; border-radius: 2.5mm; overflow: hidden;
        }
        .bloc h2 {
            font-size: 8.5pt; text-transform: uppercase; letter-spacing: 1.5px;
            padding: 2.5mm 5mm; color: #fff; background: #1e3a8a;
        }
        .bloc.client h2 { background: #0f766e; }
        .bloc .contenu { padding: 4mm 5mm; font-size: 9.5pt; line-height: 1.75; color: #334155; }
        .bloc .contenu strong { color: #0f172a; font-size: 10.5pt; }

        .objet-ligne {
            margin-top: 6mm; background: #eff6ff; border-left: 1.2mm solid #2563eb;
            border-radius: 0 2mm 2mm 0; padding: 3mm 5mm; font-size: 10pt;
        }
        .objet-ligne strong { color: #1e3a8a; }

        /* ===== Tableau ===== */
        table { width: 100%; border-collapse: collapse; margin-top: 7mm; }
        thead th {
            background: #1e3a8a; color: #fff; font-size: 8.5pt; text-transform: uppercase;
            letter-spacing: .8px; padding: 3.5mm 3mm;
        }
        th.c, td.c { text-align: center; }
        th.r, td.r { text-align: right; }
        th:first-child { text-align: left; border-radius: 2mm 0 0 0; }
        th:last-child { border-radius: 0 2mm 0 0; background: #f59e0b; }
        tbody td { padding: 4mm 3mm; font-size: 10pt; border-bottom: 1px solid #e2e8f0; }
        tbody tr:nth-child(even) { background: #f8fafc; }
        td.ref { color: #94a3b8; font-size: 8.5pt; width: 10mm; }

        /* ===== Totaux ===== */
        .totaux { display: flex; justify-content: space-between; gap: 8mm; margin-top: 6mm; align-items: flex-start; }
        .lettres {
            flex: 1; background: #f8fafc; border: 1px dashed #94a3b8; border-radius: 2.5mm;
            padding: 4mm 5mm; font-size: 9pt; color: #475569; line-height: 1.6;
        }
        .lettres strong { color: #0f172a; }
        .carte-totaux { width: 75mm; border: 1px solid #e2e8f0; border-radius: 2.5mm; overflow: hidden; }
        .ligne-total {
            display: flex; justify-content: space-between; padding: 2.8mm 5mm;
            font-size: 10pt; border-bottom: 1px solid #eef2f7;
        }
        .ligne-total:last-child { border-bottom: none; }
        .ligne-total.ttc {
            background: linear-gradient(135deg, #1e3a8a, #2563eb); color: #fff; font-weight: 700;
            font-size: 11.5pt; padding: 4mm 5mm;
        }

        /* ===== Conditions & paiement ===== */
        .bas { display: flex; gap: 8mm; margin-top: 8mm; }
        .col-bas { flex: 1; font-size: 8.5pt; color: #64748b; line-height: 1.8; }
        .col-bas h3 {
            font-size: 9pt; color: #1e3a8a; text-transform: uppercase; letter-spacing: 1px;
            border-bottom: 1px solid #e2e8f0; padding-bottom: 1.5mm; margin-bottom: 2mm;
        }
        .paiement-item { display: flex; align-items: center; gap: 2.5mm; }
        .puce { width: 4.5mm; height: 4.5mm; border-radius: 50%; background: #10b981; display: inline-block; flex-shrink: 0; }

        /* ===== Signatures ===== */
        .signatures { display: flex; justify-content: space-between; margin-top: 10mm; padding: 0 10mm; }
        .signature { width: 62mm; text-align: center; font-size: 9.5pt; }
        .signature .cadre {
            margin-top: 3mm; height: 22mm; border: 1px dotted #94a3b8; border-radius: 2mm;
            display: flex; align-items: center; justify-content: center; color: #cbd5e1; font-size: 8pt;
        }
        .signature.client .cadre { border-color: #0f766e; }

        /* ===== Pied de page ===== */
        .pied {
            position: absolute; bottom: 0; left: 0; right: 0;
            background: #1e3a8a; color: #bfdbfe; text-align: center;
            font-size: 8pt; padding: 4mm; letter-spacing: .5px;
        }
        .pied strong { color: #fff; }

        /* ===== Bouton (invisible à l'impression) ===== */
        .barre-impression { text-align: center; padding: 8mm; }
        .barre-impression button {
            background: #2563eb; color: #fff; border: none; font-size: 12pt; font-weight: 600;
            padding: 4mm 12mm; border-radius: 3mm; cursor: pointer;
        }
        .barre-impression button:hover { background: #1d4ed8; }
        @media print {
            body { background: #fff; }
            .barre-impression { display: none; }
            .page { margin: 0; }
        }
    </style>
</head>
<body>

<div class="barre-impression">
    <button onclick="window.print()">🖨️ Imprimer / Enregistrer en PDF</button>
    <p style="margin-top:3mm;font-size:9pt;color:#64748b;">
        Dans la fenêtre d'impression, choisissez « Enregistrer au format PDF » puis envoyez le fichier sur WhatsApp.
    </p>
</div>

<div class="page">

    <!-- Bandeau d'en-tête -->
    <div class="bandeau">
        <img src="<?= $logo_base64 ?>" alt="Logo">
        <div class="doc">
            <h1>PROFORMA</h1>
            <span class="num"><?= e($devis['numero']) ?></span>
            <p class="date">Émis le <?= e(date_fr($devis['cree_le'])) ?></p>
        </div>
    </div>
    <div class="bande"></div>

    <div class="corps">

        <!-- Émetteur / Client -->
        <div class="blocs">
            <div class="bloc">
                <h2>Émetteur</h2>
                <div class="contenu">
                    <strong>DTS Informatique</strong><br>
                    Services &amp; solutions informatiques<br>
                    ✉️ <?= e(SITE_EMAIL) ?><br>
                    📞 <?= e(SITE_TELEPHONE) ?><br>
                    💬 WhatsApp : <?= e(SITE_WHATSAPP) ?>
                </div>
            </div>
            <div class="bloc client">
                <h2>Destinataire</h2>
                <div class="contenu">
                    <strong><?= e($devis['client_nom']) ?></strong><br>
                    📞 <?= e($devis['client_telephone']) ?><br>
                    <?php if ($devis['client_email']): ?>✉️ <?= e($devis['client_email']) ?><?php endif; ?>
                    <br>Statut : <em><?= statut_devis($devis['statut'])[0] ?></em>
                </div>
            </div>
        </div>

        <!-- Objet -->
        <?php if ($devis['objet']): ?>
        <div class="objet-ligne"><strong>Objet :</strong> <?= e($devis['objet']) ?></div>
        <?php endif; ?>

        <!-- Détail des prestations -->
        <table>
            <thead>
                <tr>
                    <th class="c" style="text-align:center">N°</th>
                    <th>Désignation</th>
                    <th class="c">Qté</th>
                    <th class="r">P.U. (FCFA)</th>
                    <th class="r">Montant (FCFA)</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($devis['lignes'] as $i => $ligne): ?>
                <tr>
                    <td class="ref c"><?= $i + 1 ?></td>
                    <td><?= nl2br(e($ligne['designation'])) ?></td>
                    <td class="c"><?= e(rtrim(rtrim((string) $ligne['quantite'], '0'), '.')) ?: '0' ?></td>
                    <td class="r"><?= number_format((float) $ligne['prix_unitaire'], 0, ',', ' ') ?></td>
                    <td class="r"><strong><?= number_format((float) $ligne['quantite'] * (float) $ligne['prix_unitaire'], 0, ',', ' ') ?></strong></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <!-- Totaux + montant en lettres -->
        <div class="totaux">
            <div class="lettres">
                <strong>Arrêtée la présente facture proforma à la somme de :</strong><br>
                <?= ucfirst(en_lettres($total_ttc)) ?> francs CFA TTC
            </div>
            <div class="carte-totaux">
                <div class="ligne-total"><span>Total HT</span><strong><?= number_format($total_ht, 0, ',', ' ') ?> F</strong></div>
                <?php if ($tva_taux > 0): ?>
                <div class="ligne-total"><span>TVA (<?= (int) ($tva_taux * 100) ?> %)</span><strong><?= number_format($total_tva, 0, ',', ' ') ?> F</strong></div>
                <?php endif; ?>
                <div class="ligne-total ttc"><span>Total TTC</span><strong><?= number_format($total_ttc, 0, ',', ' ') ?> F</strong></div>
            </div>
        </div>

        <!-- Conditions & paiement -->
        <div class="bas">
            <div class="col-bas">
                <h3>Conditions</h3>
                • Validité : <strong>15 jours</strong> à compter de la date d'émission<br>
                • Acompte de <strong>50 %</strong> à la commande, solde à la livraison<br>
                • Ce document ne constitue pas une facture définitive<br>
                • Marchandise vendue ni reprise ni échangée après signature
            </div>
            <div class="col-bas">
                <h3>Modalités de paiement</h3>
                <span class="paiement-item"><span class="puce"></span> Espèces (à nos bureaux)</span><br>
                <span class="paiement-item"><span class="puce"></span> Mobile Money / Wave / Orange Money</span><br>
                <span class="paiement-item"><span class="puce"></span> Virement bancaire (RIB sur demande)</span><br>
                Pour toute question : WhatsApp <?= e(SITE_WHATSAPP) ?>
            </div>
        </div>

        <!-- Signatures -->
        <div class="signatures">
            <div class="signature client">
                <strong>Le client</strong>
                <div class="cadre">« Bon pour accord » + signature</div>
            </div>
            <div class="signature">
                <strong>Pour DTS Informatique</strong>
                <div class="cadre">Signature et cachet</div>
            </div>
        </div>
    </div>

    <div class="pied">
        <strong>DTS Informatique</strong> — Devis <?= e($devis['numero']) ?> — <?= e(SITE_EMAIL) ?> — WhatsApp <?= e(SITE_WHATSAPP) ?>
    </div>
</div>

<script>
    window.addEventListener('load', () => setTimeout(() => window.print(), 400));
</script>
</body>
</html>
