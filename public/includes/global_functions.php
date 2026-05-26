<?php
/**
 * Global SQL Functions
 * Simplified database query helpers for Braingonizer
 */

require_once __DIR__ . '/../src/db_connection.php';

// Execute a SELECT query and return results

function get_sql(string $sql): array {
    global $pdo;
    
    try {
        $stmt = $pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("get_sql() error: " . $e->getMessage() . " | Query: " . $sql);
        return [];
    }
}

// Execute an INSERT, UPDATE, or DELETE query with prepared statement support
// Returns number of affected rows on success, false on failure

/**
 * @param string $sql SQL query with ? placeholders
 * @param array $params Parameters to bind to the query
 * @return int|false Number of affected rows or false on failure
 */
function run_sql(string $sql, array $params = []) {
    global $pdo;
    
    if ($pdo === null) {
        error_log("run_sql() error: PDO connection is null");
        return false;
    }
    
    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->rowCount();
    } catch (PDOException $e) {
        error_log("run_sql() error: " . $e->getMessage() . " | Query: " . $sql . " | Params: " . json_encode($params));
        return false;
    }
}

require_once $_SERVER['DOCUMENT_ROOT'] . '/src/helpers/settings_helpers.php';

/**
 * Debug logging wrapper - only logs if debugging is enabled
 * @param string $message Message to log
 * @param string|null $page Page name (e.g., 'notes', 'tasks'). If null, only checks debug_global
 * @return void
 */
function debug_log($message, $page = null) {
    if ($page !== null) {
        if (isDebugEnabled($page)) {
            error_log("[DEBUG - $page] " . $message);
        }
    } else {
        $globalDebug = getSetting('debug_global');
        if ($globalDebug === '1') {
            error_log("[DEBUG] " . $message);
        }
    }
}

/**
 * Debug SQL query logging
 * @param string $sql SQL query
 * @param array $params Query parameters
 * @param string $page Page name
 * @return void
 */
function debug_query($sql, $params, $page) {
    if (isDebugEnabled($page)) {
        error_log("[DEBUG - $page - QUERY] SQL: $sql | Params: " . json_encode($params));
    }
}

/**
 * Debug request data logging (POST/GET)
 * @param string $page Page name
 * @return void
 */
function debug_request($page) {
    if (isDebugEnabled($page)) {
        error_log("[DEBUG - $page - REQUEST] POST: " . json_encode($_POST) . " | GET: " . json_encode($_GET));
    }
}

/**
 * Debug response logging
 * @param mixed $data Response data to log
 * @param string $page Page name
 * @return void
 */
function debug_response($data, $page) {
    if (isDebugEnabled($page)) {
        error_log("[DEBUG - $page - RESPONSE] " . json_encode($data));
    }
}

/**
 * Start a performance timer
 * @param string $label Timer label
 * @param string $page Page name
 * @return void
 */
function debug_timer_start($label, $page) {
    if (isDebugEnabled($page)) {
        global $debug_timers;
        if (!isset($debug_timers)) {
            $debug_timers = [];
        }
        $debug_timers[$label] = microtime(true);
    }
}

/**
 * End a performance timer and log duration
 * @param string $label Timer label
 * @param string $page Page name
 * @return void
 */
function debug_timer_end($label, $page) {
    if (isDebugEnabled($page)) {
        global $debug_timers;
        if (isset($debug_timers[$label])) {
            $duration = microtime(true) - $debug_timers[$label];
            error_log("[DEBUG - $page - TIMER] $label: " . number_format($duration * 1000, 2) . "ms");
            unset($debug_timers[$label]);
        }
    }
}
