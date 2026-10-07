<?php
/**
 * Configuration de la connexion à la base de données.
 * Modifiez ces constantes selon votre environnement.
 */
define('DB_HOST', 'localhost');
define('DB_NAME', 'dsi_site');
define('DB_USER', 'root');
define('DB_PASS', '');

define('SITE_NAME', 'DTS');
define('SITE_TAGLINE', 'DTS Informatique — Services & solutions informatiques');
define('SITE_EMAIL', 'Dts.informatique23@gmail.com');
define('SITE_TELEPHONE', '0757756527');
define('SITE_WHATSAPP', '0503256284');
define('WHATSAPP_LIEN', 'https://wa.me/2250503256284'); // +225 (Côte d'Ivoire) + numéro sans le 0
define('SITE_MAPS', 'https://maps.app.goo.gl/qsp7qTV2q1UfKc2NA');

/**
 * Retourne une connexion PDO partagée (singleton).
 */
function db(): PDO
{
    static $pdo = null;
    if ($pdo === null) {
        try {
            $pdo = new PDO(
                'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4',
                DB_USER,
                DB_PASS,
                [
                    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES   => false,
                ]
            );
        } catch (PDOException $e) {
            http_response_code(500);
            die('Erreur de connexion à la base de données. Vérifiez le fichier includes/config.php.');
        }
    }
    return $pdo;
}
