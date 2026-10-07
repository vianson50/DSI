<?php
/**
 * Vérifie que l'administrateur est connecté.
 * À inclure en tête de chaque page admin protégée.
 */
require_once __DIR__ . '/../includes/functions.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (empty($_SESSION['admin_id'])) {
    header('Location: ../admin/login.php');
    exit;
}
