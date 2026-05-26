<?php
/**
 * Reminder Helper Functions
 * Backend utilities for managing reminders in Braingonizer
 */

require_once __DIR__ . '/../../db_connection.php';
require_once __DIR__ . '/../../../includes/global_functions.php';
require_once __DIR__ . '/tag_helpers.php';

/**
 * Handle all POST/AJAX requests for reminders
 * @return void Outputs JSON and exits if POST request
 */
function handleReminderRequest() {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['action'])) {
        return; // Not a POST request, continue to page rendering
    }
    
    // Skip if this request is for a different type
    if (isset($_POST['type']) && $_POST['type'] !== 'reminder') {
        return;
    }
    
    header('Content-Type: application/json');
    
    switch ($_POST['action']) {
        case 'create':
            $title = $_POST['title'] ?? '';
            $description = $_POST['description'] ?? '';
            $color = $_POST['color'] ?? 'yellow';
            $tagNames = isset($_POST['tags']) ? json_decode($_POST['tags'], true) : [];
            
            $reminderId = createReminder($title, $description, $color, $tagNames);
            if ($reminderId) {
                echo json_encode(['success' => true, 'id' => $reminderId, 'message' => 'Reminder created successfully']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to create reminder']);
            }
            break;
            
        case 'update':
            $id = $_POST['id'] ?? 0;
            $title = $_POST['title'] ?? '';
            $description = $_POST['description'] ?? '';
            $color = $_POST['color'] ?? 'yellow';
            $tagNames = isset($_POST['tags']) ? json_decode($_POST['tags'], true) : [];
            
            if (updateReminder($id, $title, $description, $color, $tagNames)) {
                echo json_encode(['success' => true, 'message' => 'Reminder updated successfully']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to update reminder']);
            }
            break;
            
        case 'delete':
            $id = $_POST['id'] ?? 0;
            if (!$id) {
                echo json_encode(['success' => false, 'message' => 'Invalid reminder ID']);
                break;
            }
            
            $result = deleteReminder($id);
            if ($result) {
                echo json_encode(['success' => true, 'message' => 'Reminder deleted successfully']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Unable to delete reminder. It may not exist or there was a database error.']);
            }
            break;
            
        case 'get_tags':
            $tags = getAllTags();
            echo json_encode(['success' => true, 'tags' => $tags]);
            break;
            
        case 'get_reminder':
            $id = $_POST['id'] ?? 0;
            $reminder = getReminderById($id);
            if ($reminder) {
                echo json_encode(['success' => true, 'reminder' => $reminder]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Reminder not found']);
            }
            break;
    }
    
    exit;
}

/**
 * Get all reminders
 * @return array Array of all reminders with their tags
 */
function getAllReminders() {
    $reminders = get_sql("SELECT * FROM reminders ORDER BY created_at DESC");
    
    // Attach tags to each reminder
    foreach ($reminders as &$reminder) {
        $reminder['tags'] = getItemTags('reminder', $reminder['id']);
    }
    
    return $reminders;
}

/**
 * Get a single reminder by ID
 * @param int $id Reminder ID
 * @return array|null Reminder data with tags or null if not found
 */
function getReminderById($id) {
    global $pdo;
    try {
        $stmt = $pdo->prepare("SELECT * FROM reminders WHERE id = ?");
        $stmt->execute([$id]);
        $reminder = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($reminder) {
            $reminder['tags'] = getItemTags('reminder', $reminder['id']);
        }
        
        return $reminder;
    } catch (PDOException $e) {
        error_log("Error fetching reminder: " . $e->getMessage());
        return null;
    }
}

/**
 * Create a new reminder
 * @param string $title Reminder title
 * @param string $description Reminder description
 * @param string $color Reminder card color
 * @param array $tagNames Array of tag names (max 3)
 * @return int|false Reminder ID on success, false on failure
 */
function createReminder($title, $description, $color, $tagNames = []) {
    global $pdo;
    try {
        // Validate tag limit
        if (count($tagNames) > 3) {
            return false;
        }
        
        // Process tags - get or create tag IDs
        $tagIds = [];
        if (!empty($tagNames)) {
            foreach ($tagNames as $tagName) {
                $tagName = trim($tagName);
                if (!empty($tagName)) {
                    $tagId = getOrCreateTag($tagName);
                    if ($tagId) {
                        $tagIds[] = $tagId;
                    }
                }
            }
        }
        
        // Convert tag IDs to comma-separated string
        $tagsString = !empty($tagIds) ? implode(',', $tagIds) : null;
        
        // Create reminder with tags
        $stmt = $pdo->prepare("INSERT INTO reminders (title, description, color, tags) VALUES (?, ?, ?, ?)");
        $stmt->execute([$title, $description, $color, $tagsString]);
        return $pdo->lastInsertId();
    } catch (PDOException $e) {
        error_log("Error creating reminder: " . $e->getMessage());
        return false;
    }
}

/**
 * Update an existing reminder
 * @param int $id Reminder ID
 * @param string $title Reminder title
 * @param string $description Reminder description
 * @param string $color Reminder card color
 * @param array $tagNames Array of tag names (max 3)
 * @return bool Success status
 */
function updateReminder($id, $title, $description, $color, $tagNames = []) {
    global $pdo;
    try {
        // Validate tag limit
        if (count($tagNames) > 3) {
            return false;
        }
        
        // Process tags - get or create tag IDs
        $tagIds = [];
        foreach ($tagNames as $tagName) {
            $tagName = trim($tagName);
            if (!empty($tagName)) {
                $tagId = getOrCreateTag($tagName);
                if ($tagId) {
                    $tagIds[] = $tagId;
                }
            }
        }
        
        // Convert tag IDs to comma-separated string
        $tagsString = !empty($tagIds) ? implode(',', $tagIds) : null;
        
        // Update reminder with tags
        $stmt = $pdo->prepare("UPDATE reminders SET title = ?, description = ?, color = ?, tags = ? WHERE id = ?");
        return $stmt->execute([$title, $description, $color, $tagsString, $id]);
    } catch (PDOException $e) {
        error_log("Error updating reminder: " . $e->getMessage());
        return false;
    }
}

/**
 * Delete a reminder
 * @param int $id Reminder ID
 * @return bool Success status
 */
function deleteReminder($id) {
    $result = run_sql("DELETE FROM reminders WHERE id = ?", [$id]);
    return $result !== false;
}
