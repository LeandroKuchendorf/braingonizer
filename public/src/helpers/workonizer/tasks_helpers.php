<?php
/**
 * Work Tasks Helper Functions
 * Handles all database operations and POST request handling for work tasks
 */

require_once __DIR__ . '/../../db_connection.php';
require_once __DIR__ . '/../../../includes/global_functions.php';
require_once __DIR__ . '/../braingonizer/tag_helpers.php';

/**
 * Get all work tasks
 * @return array Array of all work tasks with their associated tags
 */
function getAllWorkTasks() {
    global $pdo;
    try {
        $stmt = $pdo->query("
            SELECT id, title, description, priority, status, due_date, tags, created_at, updated_at
            FROM work_tasks
            ORDER BY created_at DESC
        ");
        $tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Get tags for each task
        foreach ($tasks as &$task) {
            $task['tags'] = getItemTags('work_task', $task['id']);
        }
        
        return $tasks;
    } catch (Exception $e) {
        error_log("Error fetching work tasks: " . $e->getMessage());
        return [];
    }
}

/**
 * Get work task by ID
 * @param int $taskId Task ID
 * @return array|null Task data with tags or null if not found
 */
function getWorkTaskById($taskId) {
    global $pdo;
    try {
        $stmt = $pdo->prepare("
            SELECT id, title, description, priority, status, due_date, tags, created_at, updated_at
            FROM work_tasks
            WHERE id = ?
        ");
        $stmt->execute([$taskId]);
        $task = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($task) {
            $task['tags'] = getItemTags('work_task', $taskId);
        }
        
        return $task;
    } catch (Exception $e) {
        error_log("Error fetching work task: " . $e->getMessage());
        return null;
    }
}

/**
 * Create a new work task
 * @param string $title Task title
 * @param string $description Task description
 * @param string $priority Priority level (high, medium, low)
 * @param string $status Task status (pending, in_progress, completed, cancelled)
 * @param string $dueDate Due date (YYYY-MM-DD)
 * @param array $tagNames Array of tag names to associate with task
 * @return int|false Task ID on success, false on failure
 */
function createWorkTask($title, $description, $priority, $status, $dueDate, $tagNames = []) {
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
        
        $stmt = $pdo->prepare("
            INSERT INTO work_tasks (title, description, priority, status, due_date, tags, created_at, updated_at)
            VALUES (?, ?, ?, ?, ?, ?, NOW(), NOW())
        ");
        $stmt->execute([$title, $description, $priority, $status, $dueDate, $tagsString]);
        return $pdo->lastInsertId();
    } catch (Exception $e) {
        error_log("Error creating work task: " . $e->getMessage());
        return false;
    }
}

/**
 * Update an existing work task
 * @param int $taskId Task ID
 * @param string $title Task title
 * @param string $description Task description
 * @param string $priority Priority level
 * @param string $status Task status
 * @param string $dueDate Due date
 * @param array $tagNames Array of tag names
 * @return bool Success status
 */
function updateWorkTask($taskId, $title, $description, $priority, $status, $dueDate, $tagNames = []) {
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
        
        $stmt = $pdo->prepare("
            UPDATE work_tasks
            SET title = ?, description = ?, priority = ?, status = ?, due_date = ?, tags = ?, updated_at = NOW()
            WHERE id = ?
        ");
        return $stmt->execute([$title, $description, $priority, $status, $dueDate, $tagsString, $taskId]);
    } catch (Exception $e) {
        error_log("Error updating work task: " . $e->getMessage());
        return false;
    }
}

/**
 * Delete a work task
 * @param int $taskId Task ID
 * @return bool Success status
 */
function deleteWorkTask($taskId) {
    $result = run_sql("DELETE FROM work_tasks WHERE id = ?", [$taskId]);
    return $result !== false;
}


/**
 * Handle all POST/AJAX requests for work tasks
 * @return void Outputs JSON and exits if POST request
 */
function handleWorkTaskRequest() {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['action'])) {
        return; // Not a POST request, continue to page rendering
    }
    
    header('Content-Type: application/json');
    
    switch ($_POST['action']) {
        case 'create':
            $title = $_POST['title'] ?? '';
            $description = $_POST['description'] ?? '';
            $priority = $_POST['priority'] ?? 'medium';
            $status = $_POST['status'] ?? 'pending';
            $dueDate = $_POST['due_date'] ?? null;
            $tagNames = isset($_POST['tags']) ? json_decode($_POST['tags'], true) : [];
            
            $taskId = createWorkTask($title, $description, $priority, $status, $dueDate, $tagNames);
            if ($taskId) {
                echo json_encode(['success' => true, 'id' => $taskId, 'message' => 'Task created successfully']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to create task']);
            }
            break;
            
        case 'update':
            $id = $_POST['id'] ?? 0;
            $title = $_POST['title'] ?? '';
            $description = $_POST['description'] ?? '';
            $priority = $_POST['priority'] ?? 'medium';
            $status = $_POST['status'] ?? 'pending';
            $dueDate = $_POST['due_date'] ?? null;
            $tagNames = isset($_POST['tags']) ? json_decode($_POST['tags'], true) : [];
            
            if (updateWorkTask($id, $title, $description, $priority, $status, $dueDate, $tagNames)) {
                echo json_encode(['success' => true, 'message' => 'Task updated successfully']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to update task']);
            }
            break;
            
        case 'delete':
            $id = $_POST['id'] ?? 0;
            if (!$id) {
                echo json_encode(['success' => false, 'message' => 'Invalid task ID']);
                break;
            }
            
            $result = deleteWorkTask($id);
            if ($result) {
                echo json_encode(['success' => true, 'message' => 'Task deleted successfully']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Unable to delete task. It may not exist or there was a database error.']);
            }
            break;
            
        case 'get_task':
            $id = $_POST['id'] ?? 0;
            $task = getWorkTaskById($id);
            if ($task) {
                echo json_encode(['success' => true, 'task' => $task]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Task not found']);
            }
            break;
            
        case 'get_tags':
            $tags = getAllTags();
            echo json_encode(['success' => true, 'tags' => $tags]);
            break;
    }
    
    exit;
}
