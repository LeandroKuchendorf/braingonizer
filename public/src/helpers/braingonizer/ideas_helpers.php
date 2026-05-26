<?php
/**
 * Ideas Helper Functions
 * Handles all database operations and POST request handling for ideas
 */

require_once __DIR__ . '/../../db_connection.php';
require_once __DIR__ . '/../../../includes/global_functions.php';

/**
 * Get all ideas
 * @return array Array of all ideas
 */
function getAllIdeas() {
    global $pdo;
    try {
        $stmt = $pdo->query("
            SELECT i.id, i.title, i.description, i.category_id, i.priority, i.status, 
                   i.created_at, i.updated_at, c.name as category_name, c.color as category_color
            FROM ideas i
            LEFT JOIN categories c ON i.category_id = c.id
            ORDER BY i.created_at DESC
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        error_log("Error fetching ideas: " . $e->getMessage());
        return [];
    }
}

/**
 * Get idea by ID
 * @param int $ideaId Idea ID
 * @return array|null Idea data or null if not found
 */
function getIdeaById($ideaId) {
    global $pdo;
    try {
        $stmt = $pdo->prepare("
            SELECT i.id, i.title, i.description, i.category_id, i.priority, i.status, 
                   i.created_at, i.updated_at, c.name as category_name, c.color as category_color
            FROM ideas i
            LEFT JOIN categories c ON i.category_id = c.id
            WHERE i.id = ?
        ");
        $stmt->execute([$ideaId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        error_log("Error fetching idea: " . $e->getMessage());
        return null;
    }
}

/**
 * Create a new idea
 * @param string $title Idea title
 * @param string $description Idea description
 * @param int|null $categoryId Category ID (foreign key to categories table)
 * @param string $priority Priority level (high, medium, low, very-low)
 * @param string $status Idea status (concept, in-progress, completed)
 * @return int|false Idea ID on success, false on failure
 */
function createIdea($title, $description, $categoryId, $priority, $status) {
    global $pdo;
    try {
        $stmt = $pdo->prepare("
            INSERT INTO ideas (title, description, category_id, priority, status, created_at, updated_at)
            VALUES (?, ?, ?, ?, ?, NOW(), NOW())
        ");
        $stmt->execute([$title, $description, $categoryId, $priority, $status]);
        return $pdo->lastInsertId();
    } catch (Exception $e) {
        error_log("Error creating idea: " . $e->getMessage());
        return false;
    }
}

/**
 * Update an existing idea
 * @param int $ideaId Idea ID
 * @param string $title Idea title
 * @param string $description Idea description
 * @param int|null $categoryId Category ID (foreign key to categories table)
 * @param string $priority Priority level
 * @param string $status Idea status
 * @return bool Success status
 */
function updateIdea($ideaId, $title, $description, $categoryId, $priority, $status) {
    global $pdo;
    try {
        $stmt = $pdo->prepare("
            UPDATE ideas
            SET title = ?, description = ?, category_id = ?, priority = ?, status = ?, updated_at = NOW()
            WHERE id = ?
        ");
        $stmt->execute([$title, $description, $categoryId, $priority, $status, $ideaId]);
        return true;
    } catch (Exception $e) {
        error_log("Error updating idea: " . $e->getMessage());
        return false;
    }
}

/**
 * Delete an idea
 * @param int $ideaId Idea ID
 * @return bool Success status
 */
function deleteIdea($ideaId) {
    $result = run_sql("DELETE FROM ideas WHERE id = ?", [$ideaId]);
    return $result !== false;
}

/**
 * Get all idea categories
 * @return array Array of category objects with id, name, and color
 */
function getIdeaCategories() {
    global $pdo;
    try {
        $stmt = $pdo->query("
            SELECT id, name, color, created_at
            FROM categories
            ORDER BY name ASC
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        error_log("Error fetching idea categories: " . $e->getMessage());
        return [];
    }
}

/**
 * Get idea category by ID
 * @param int $categoryId Category ID
 * @return array|null Category data or null if not found
 */
function getIdeaCategoryById($categoryId) {
    global $pdo;
    try {
        $stmt = $pdo->prepare("
            SELECT id, name, color, created_at, updated_at
            FROM categories
            WHERE id = ?
        ");
        $stmt->execute([$categoryId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        error_log("Error fetching idea category: " . $e->getMessage());
        return null;
    }
}

/**
 * Create a new idea category
 * @param string $name Category name
 * @param string $color Bootstrap color class
 * @return int|false Category ID on success, false on failure
 */
function createIdeaCategory($name, $color) {
    global $pdo;
    try {
        $stmt = $pdo->prepare("
            INSERT INTO categories (name, color, created_at, updated_at)
            VALUES (?, ?, NOW(), NOW())
        ");
        $stmt->execute([$name, $color]);
        return $pdo->lastInsertId();
    } catch (Exception $e) {
        error_log("Error creating idea category: " . $e->getMessage());
        return false;
    }
}

/**
 * Update an idea category
 * @param int $categoryId Category ID
 * @param string $name Category name
 * @param string $color Bootstrap color class
 * @return bool Success status
 */
function updateIdeaCategory($categoryId, $name, $color) {
    global $pdo;
    try {
        $stmt = $pdo->prepare("
            UPDATE categories
            SET name = ?, color = ?, updated_at = NOW()
            WHERE id = ?
        ");
        $stmt->execute([$name, $color, $categoryId]);
        return true;
    } catch (Exception $e) {
        error_log("Error updating idea category: " . $e->getMessage());
        return false;
    }
}

/**
 * Delete an idea category
 * @param int $categoryId Category ID
 * @return bool Success status
 */
function deleteIdeaCategory($categoryId) {
    global $pdo;
    try {
        $stmt = $pdo->prepare("DELETE FROM categories WHERE id = ?");
        $stmt->execute([$categoryId]);
        return true;
    } catch (Exception $e) {
        error_log("Error deleting idea category: " . $e->getMessage());
        return false;
    }
}

/**
 * Handle all POST/AJAX requests for ideas
 * @return void Outputs JSON and exits if POST request
 */
function handleIdeaRequest() {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['action'])) {
        return; // Not a POST request, continue to page rendering
    }
    
    header('Content-Type: application/json');
    
    switch ($_POST['action']) {
        case 'create':
            $title = $_POST['title'] ?? '';
            $description = $_POST['description'] ?? '';
            $categoryId = $_POST['category_id'] ?? null;
            $priority = $_POST['priority'] ?? 'medium';
            $status = $_POST['status'] ?? 'concept';
            
            $ideaId = createIdea($title, $description, $categoryId, $priority, $status);
            if ($ideaId) {
                echo json_encode(['success' => true, 'id' => $ideaId, 'message' => 'Idea created successfully']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to create idea']);
            }
            break;
            
        case 'update':
            $id = $_POST['id'] ?? 0;
            $title = $_POST['title'] ?? '';
            $description = $_POST['description'] ?? '';
            $categoryId = $_POST['category_id'] ?? null;
            $priority = $_POST['priority'] ?? 'medium';
            $status = $_POST['status'] ?? 'concept';
            
            if (updateIdea($id, $title, $description, $categoryId, $priority, $status)) {
                echo json_encode(['success' => true, 'message' => 'Idea updated successfully']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to update idea']);
            }
            break;
            
        case 'delete':
            $id = $_POST['id'] ?? 0;
            if (!$id) {
                echo json_encode(['success' => false, 'message' => 'Invalid idea ID']);
                break;
            }
            
            $result = deleteIdea($id);
            if ($result) {
                echo json_encode(['success' => true, 'message' => 'Idea deleted successfully']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Unable to delete idea. It may not exist or there was a database error.']);
            }
            break;
            
        case 'get_idea':
            $id = $_POST['id'] ?? 0;
            $idea = getIdeaById($id);
            if ($idea) {
                echo json_encode(['success' => true, 'idea' => $idea]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Idea not found']);
            }
            break;
            
        case 'create_category':
            $name = $_POST['name'] ?? '';
            $color = $_POST['color'] ?? 'secondary';
            
            if (!$name) {
                echo json_encode(['success' => false, 'message' => 'Category name is required']);
                break;
            }
            
            $categoryId = createIdeaCategory($name, $color);
            if ($categoryId) {
                echo json_encode(['success' => true, 'id' => $categoryId, 'message' => 'Category created successfully']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to create category']);
            }
            break;
            
        case 'delete_category':
            $id = $_POST['id'] ?? 0;
            if (deleteIdeaCategory($id)) {
                echo json_encode(['success' => true, 'message' => 'Category deleted successfully']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to delete category']);
            }
            break;
    }
    
    exit;
}
