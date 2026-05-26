<?php
/**
 * Braingonizer - Helper Functions
 */

/**
 * Safely escape output for HTML.
 */
function e(string $value): string {
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

/**
 * Get the base URL of the application.
 */
function base_url(): string {
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    return "{$protocol}://{$host}";
}
