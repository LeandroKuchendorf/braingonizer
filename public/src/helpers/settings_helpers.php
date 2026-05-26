<?php
/**
 * Settings Helper Functions
 * Backend utilities for managing application settings in Braingonizer
 */

require_once __DIR__ . '/../db_connection.php';
require_once __DIR__ . '/../../includes/global_functions.php';

/**
 * Handle all POST/AJAX requests for settings
 * @return void Outputs JSON and exits if POST request
 */
function handleSettingsRequest() {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['action'])) {
        return;
    }
    
    header('Content-Type: application/json');
    
    switch ($_POST['action']) {
        case 'get_all':
            $settings = getAllSettings();
            echo json_encode(['success' => true, 'settings' => $settings]);
            break;
            
        case 'update_single':
            $key = $_POST['key'] ?? '';
            $value = $_POST['value'] ?? '0';
            
            if (updateSetting($key, $value)) {
                echo json_encode(['success' => true, 'message' => 'Setting updated successfully']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to update setting']);
            }
            break;
            
        case 'update_global':
            $value = $_POST['value'] ?? '0';
            
            if ($value === '1') {
                if (enableAllDebug()) {
                    echo json_encode(['success' => true, 'message' => 'All debugging enabled']);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Failed to enable all debugging']);
                }
            } else {
                if (disableAllDebug()) {
                    echo json_encode(['success' => true, 'message' => 'All debugging disabled']);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Failed to disable all debugging']);
                }
            }
            break;
            
        default:
            echo json_encode(['success' => false, 'message' => 'Invalid action']);
            break;
    }
    
    exit;
}

/**
 * Get a single setting value by key
 * @param string $key Setting key
 * @return string|null Setting value or null if not found
 */
function getSetting($key) {
    global $pdo;
    
    try {
        $stmt = $pdo->prepare("SELECT setting_value FROM settings WHERE setting_key = ?");
        $stmt->execute([$key]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? $result['setting_value'] : null;
    } catch (PDOException $e) {
        error_log("Error fetching setting: " . $e->getMessage());
        return null;
    }
}

/**
 * Get all settings as an associative array
 * @return array Associative array of setting_key => setting_value
 */
function getAllSettings() {
    global $pdo;
    
    try {
        $stmt = $pdo->query("SELECT setting_key, setting_value FROM settings");
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $settings = [];
        foreach ($results as $row) {
            $settings[$row['setting_key']] = $row['setting_value'];
        }
        
        return $settings;
    } catch (PDOException $e) {
        error_log("Error fetching all settings: " . $e->getMessage());
        return [];
    }
}

/**
 * Update a single setting value
 * @param string $key Setting key
 * @param string $value Setting value
 * @return bool True on success, false on failure
 */
function updateSetting($key, $value) {
    global $pdo;
    
    try {
        $stmt = $pdo->prepare("UPDATE settings SET setting_value = ? WHERE setting_key = ?");
        return $stmt->execute([$value, $key]);
    } catch (PDOException $e) {
        error_log("Error updating setting: " . $e->getMessage());
        return false;
    }
}

/**
 * Enable all debug settings (set all debug_* to '1')
 * @return bool True on success, false on failure
 */
function enableAllDebug() {
    global $pdo;
    
    try {
        $stmt = $pdo->prepare("UPDATE settings SET setting_value = '1' WHERE setting_key LIKE 'debug_%'");
        return $stmt->execute();
    } catch (PDOException $e) {
        error_log("Error enabling all debug: " . $e->getMessage());
        return false;
    }
}

/**
 * Disable all debug settings (set all debug_* to '0')
 * @return bool True on success, false on failure
 */
function disableAllDebug() {
    global $pdo;
    
    try {
        $stmt = $pdo->prepare("UPDATE settings SET setting_value = '0' WHERE setting_key LIKE 'debug_%'");
        return $stmt->execute();
    } catch (PDOException $e) {
        error_log("Error disabling all debug: " . $e->getMessage());
        return false;
    }
}

/**
 * Check if debugging is enabled for a specific page
 * Returns true if debug_global is ON OR the specific page debug is ON
 * @param string $page Page name (e.g., 'notes', 'tasks')
 * @return bool True if debugging is enabled, false otherwise
 */
function isDebugEnabled($page) {
    $globalDebug = getSetting('debug_global');
    $pageDebug = getSetting('debug_' . $page);
    
    return ($globalDebug === '1' || $pageDebug === '1');
}
