<?php
/**
 * Tasks Helper Functions
 * Handles all database operations and POST request handling for tasks
 */

require_once __DIR__ . '/../../db_connection.php';
require_once __DIR__ . '/../../../includes/global_functions.php';
require_once __DIR__ . '/tag_helpers.php';

/**
 * Get all tasks
 * @return array Array of all tasks with their associated tags
 */
function getAllTasks() {
    global $pdo;
    try {
        $stmt = $pdo->query("
            SELECT t.id, t.title, t.description, t.project_id, t.priority, t.status, t.due_date, t.tags, t.created_at, t.updated_at,
                   p.name AS project_name, p.color AS project_color
            FROM tasks t
            LEFT JOIN projects p ON t.project_id = p.id
            ORDER BY t.created_at DESC
        ");
        $tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Get tags for each task
        foreach ($tasks as &$task) {
            $task['tags'] = getItemTags('task', $task['id']);
        }
        
        return $tasks;
    } catch (Exception $e) {
        error_log("Error fetching tasks: " . $e->getMessage());
        return [];
    }
}

/**
 * Get task by ID
 * @param int $taskId Task ID
 * @return array|null Task data with tags or null if not found
 */
function getTaskById($taskId) {
    global $pdo;
    try {
        $stmt = $pdo->prepare("
            SELECT id, title, description, project_id, priority, status, due_date, tags, created_at, updated_at
            FROM tasks
            WHERE id = ?
        ");
        $stmt->execute([$taskId]);
        $task = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($task) {
            $task['tags'] = getItemTags('task', $taskId);
        }
        
        return $task;
    } catch (Exception $e) {
        error_log("Error fetching task: " . $e->getMessage());
        return null;
    }
}

/**
 * Create a new task
 * @param string $title Task title
 * @param string $description Task description
 * @param int $projectId Project ID
 * @param string $priority Priority level (high, medium, low, very-low)
 * @param string $status Task status (completed, in-progress, on-hold, not-started)
 * @param string $dueDate Due date (YYYY-MM-DD)
 * @param array $tagNames Array of tag names to associate with task
 * @return int|false Task ID on success, false on failure
 */
function createTask($title, $description, $projectId, $priority, $status, $dueDate, $tagNames = []) {
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
            INSERT INTO tasks (title, description, project_id, priority, status, due_date, tags, created_at, updated_at)
            VALUES (?, ?, ?, ?, ?, ?, ?, NOW(), NOW())
        ");
        $stmt->execute([$title, $description, $projectId, $priority, $status, $dueDate, $tagsString]);
        return $pdo->lastInsertId();
    } catch (Exception $e) {
        error_log("Error creating task: " . $e->getMessage());
        return false;
    }
}

/**
 * Update an existing task
 * @param int $taskId Task ID
 * @param string $title Task title
 * @param string $description Task description
 * @param int $projectId Project ID
 * @param string $priority Priority level
 * @param string $status Task status
 * @param string $dueDate Due date
 * @param array $tagNames Array of tag names
 * @return bool Success status
 */
function updateTask($taskId, $title, $description, $projectId, $priority, $status, $dueDate, $tagNames = []) {
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
            UPDATE tasks
            SET title = ?, description = ?, project_id = ?, priority = ?, status = ?, due_date = ?, tags = ?, updated_at = NOW()
            WHERE id = ?
        ");
        return $stmt->execute([$title, $description, $projectId, $priority, $status, $dueDate, $tagsString, $taskId]);
    } catch (Exception $e) {
        error_log("Error updating task: " . $e->getMessage());
        return false;
    }
}

/**
 * Delete a task
 * @param int $taskId Task ID
 * @return bool Success status
 */
function deleteTask($taskId) {
    $result = run_sql("DELETE FROM tasks WHERE id = ?", [$taskId]);
    return $result !== false;
}


/**
 * Get all projects
 * @return array Array of all projects
 */
function getAllProjects() {
    global $pdo;
    try {
        $stmt = $pdo->query("
            SELECT id, name, description, color, created_at, updated_at
            FROM projects
            ORDER BY created_at DESC
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        error_log("Error fetching projects: " . $e->getMessage());
        return [];
    }
}

/**
 * Create a new project
 * @param string $name Project name
 * @param string $description Project description
 * @param string $color Project color
 * @return int|false Project ID on success, false on failure
 */
function createProject($name, $description, $color) {
    global $pdo;
    try {
        $stmt = $pdo->prepare("
            INSERT INTO projects (name, description, color, created_at, updated_at)
            VALUES (?, ?, ?, NOW(), NOW())
        ");
        $stmt->execute([$name, $description, $color]);
        return $pdo->lastInsertId();
    } catch (Exception $e) {
        error_log("Error creating project: " . $e->getMessage());
        return false;
    }
}

/**
 * Update an existing project
 * @param int $id Project ID
 * @param string $name Project name
 * @param string $description Project description
 * @param string $color Project color
 * @return bool Success status
 */
function updateProject($id, $name, $description, $color) {
    global $pdo;
    try {
        $stmt = $pdo->prepare("
            UPDATE projects
            SET name = ?, description = ?, color = ?, updated_at = NOW()
            WHERE id = ?
        ");
        return $stmt->execute([$name, $description, $color, $id]);
    } catch (Exception $e) {
        error_log("Error updating project: " . $e->getMessage());
        return false;
    }
}

/**
 * Delete a project
 * @param int $id Project ID
 * @return bool Success status
 */
function deleteProject($id) {
    global $pdo;
    try {
        $stmt = $pdo->prepare("DELETE FROM projects WHERE id = ?");
        return $stmt->execute([$id]);
    } catch (Exception $e) {
        error_log("Error deleting project: " . $e->getMessage());
        return false;
    }
}

/**
 * Handle all POST/AJAX requests for tasks
 * @return void Outputs JSON and exits if POST request
 */
function handleTaskRequest() {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['action'])) {
        return; // Not a POST request, continue to page rendering
    }
    
    header('Content-Type: application/json');
    
    switch ($_POST['action']) {
        case 'create':
            $title = $_POST['title'] ?? '';
            $description = $_POST['description'] ?? '';
            $projectId = $_POST['project_id'] ?? 0;
            $priority = $_POST['priority'] ?? 'medium';
            $status = $_POST['status'] ?? 'not-started';
            $dueDate = $_POST['due_date'] ?? null;
            $tagNames = isset($_POST['tags']) ? json_decode($_POST['tags'], true) : [];
            
            $taskId = createTask($title, $description, $projectId, $priority, $status, $dueDate, $tagNames);
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
            $projectId = $_POST['project_id'] ?? 0;
            $priority = $_POST['priority'] ?? 'medium';
            $status = $_POST['status'] ?? 'not-started';
            $dueDate = $_POST['due_date'] ?? null;
            $tagNames = isset($_POST['tags']) ? json_decode($_POST['tags'], true) : [];
            
            if (updateTask($id, $title, $description, $projectId, $priority, $status, $dueDate, $tagNames)) {
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
            
            $result = deleteTask($id);
            if ($result) {
                echo json_encode(['success' => true, 'message' => 'Task deleted successfully']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Unable to delete task. It may not exist or there was a database error.']);
            }
            break;
            
        case 'get_task':
            $id = $_POST['id'] ?? 0;
            $task = getTaskById($id);
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
            
        case 'create_project':
            $name = $_POST['name'] ?? '';
            $description = $_POST['description'] ?? '';
            $color = $_POST['color'] ?? 'primary';
            
            $projectId = createProject($name, $description, $color);
            if ($projectId) {
                echo json_encode(['success' => true, 'id' => $projectId, 'message' => 'Project created successfully']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to create project']);
            }
            break;

        case 'update_project':
            $id = $_POST['id'] ?? 0;
            $name = $_POST['name'] ?? '';
            $description = $_POST['description'] ?? '';
            $color = $_POST['color'] ?? 'primary';

            error_log("[DEBUG] update_project hit: id=$id, name=$name, color=$color");

            if (!$id) {
                echo json_encode(['success' => false, 'message' => 'Invalid project ID']);
                break;
            }

            $updateResult = updateProject($id, $name, $description, $color);
            error_log("[DEBUG] updateProject result: " . var_export($updateResult, true));

            if ($updateResult) {
                echo json_encode(['success' => true, 'message' => 'Project updated successfully']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to update project']);
            }
            break;

        case 'delete_project':
            $id = $_POST['id'] ?? 0;

            if (!$id) {
                echo json_encode(['success' => false, 'message' => 'Invalid project ID']);
                break;
            }

            if (deleteProject($id)) {
                echo json_encode(['success' => true, 'message' => 'Project deleted successfully']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to delete project']);
            }
            break;
        default:
            echo json_encode(['success' => false, 'message' => 'Unknown action: ' . ($_POST['action'] ?? '')]);
            break;
    }
    
    exit;
}
