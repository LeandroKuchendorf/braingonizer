<?php
/**
 * Work Projects Helper Functions
 * Handles all database operations and POST request handling for work projects
 */

require_once __DIR__ . '/../../db_connection.php';
require_once __DIR__ . '/../../../includes/global_functions.php';

/**
 * Get all work projects
 * @return array Array of all work projects
 */
function getAllWorkProjects() {
    global $pdo;
    try {
        $stmt = $pdo->query("
            SELECT id, name, description, status, created_at, updated_at
            FROM work_projects
            ORDER BY created_at DESC
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        error_log("Error fetching work projects: " . $e->getMessage());
        return [];
    }
}

/**
 * Get work project by ID
 * @param int $projectId Project ID
 * @return array|null Project data or null if not found
 */
function getWorkProjectById($projectId) {
    global $pdo;
    try {
        $stmt = $pdo->prepare("
            SELECT id, name, description, status, created_at, updated_at
            FROM work_projects
            WHERE id = ?
        ");
        $stmt->execute([$projectId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        error_log("Error fetching work project: " . $e->getMessage());
        return null;
    }
}

/**
 * Create a new work project
 * @param string $name Project name
 * @param string $description Project description
 * @param string $status Project status (active, on_hold, completed)
 * @return int|false Project ID on success, false on failure
 */
function createWorkProject($name, $description, $status = 'active') {
    global $pdo;
    try {
        $stmt = $pdo->prepare("
            INSERT INTO work_projects (name, description, status, created_at, updated_at)
            VALUES (?, ?, ?, NOW(), NOW())
        ");
        $stmt->execute([$name, $description, $status]);
        return $pdo->lastInsertId();
    } catch (Exception $e) {
        error_log("Error creating work project: " . $e->getMessage());
        return false;
    }
}

/**
 * Update an existing work project
 * @param int $projectId Project ID
 * @param string $name Project name
 * @param string $description Project description
 * @param string $status Project status
 * @return bool Success status
 */
function updateWorkProject($projectId, $name, $description, $status) {
    global $pdo;
    try {
        $stmt = $pdo->prepare("
            UPDATE work_projects
            SET name = ?, description = ?, status = ?, updated_at = NOW()
            WHERE id = ?
        ");
        return $stmt->execute([$name, $description, $status, $projectId]);
    } catch (Exception $e) {
        error_log("Error updating work project: " . $e->getMessage());
        return false;
    }
}

/**
 * Delete a work project
 * @param int $projectId Project ID
 * @return bool Success status
 */
function deleteWorkProject($projectId) {
    $result = run_sql("DELETE FROM work_projects WHERE id = ?", [$projectId]);
    return $result !== false;
}

/**
 * Handle all POST/AJAX requests for work projects
 * @return void Outputs JSON and exits if POST request
 */
function handleWorkProjectRequest() {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['action'])) {
        return; // Not a POST request, continue to page rendering
    }
    
    header('Content-Type: application/json');
    
    switch ($_POST['action']) {
        case 'create':
            $name = $_POST['name'] ?? '';
            $description = $_POST['description'] ?? '';
            $status = $_POST['status'] ?? 'active';
            
            $projectId = createWorkProject($name, $description, $status);
            if ($projectId) {
                echo json_encode(['success' => true, 'id' => $projectId, 'message' => 'Project created successfully']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to create project']);
            }
            break;
            
        case 'update':
            $id = $_POST['id'] ?? 0;
            $name = $_POST['name'] ?? '';
            $description = $_POST['description'] ?? '';
            $status = $_POST['status'] ?? 'active';
            
            if (updateWorkProject($id, $name, $description, $status)) {
                echo json_encode(['success' => true, 'message' => 'Project updated successfully']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to update project']);
            }
            break;
            
        case 'delete':
            $id = $_POST['id'] ?? 0;
            if (!$id) {
                echo json_encode(['success' => false, 'message' => 'Invalid project ID']);
                break;
            }
            
            $result = deleteWorkProject($id);
            if ($result) {
                echo json_encode(['success' => true, 'message' => 'Project deleted successfully']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Unable to delete project. It may not exist or there was a database error.']);
            }
            break;
            
        case 'get_project':
            $id = $_POST['id'] ?? 0;
            $project = getWorkProjectById($id);
            if ($project) {
                echo json_encode(['success' => true, 'project' => $project]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Project not found']);
            }
            break;
    }
    
    exit;
}
