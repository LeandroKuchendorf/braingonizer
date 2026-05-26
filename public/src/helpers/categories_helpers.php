<?php
/**
 * Categories Helper Functions
 * Handles all database operations and POST request handling for global categories
 */

require_once __DIR__ . '/../db_connection.php';
require_once __DIR__ . '/../../includes/global_functions.php';

/**
 * Get all categories
 * @return array Array of all categories
 */
function getAllCategories() {
    global $pdo;
    try {
        $stmt = $pdo->query("
            SELECT id, name, color, created_at, updated_at
            FROM categories
            ORDER BY name ASC
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        error_log("Error fetching categories: " . $e->getMessage());
        return [];
    }
}

/**
 * Get category by ID
 * @param int $categoryId Category ID
 * @return array|null Category data or null if not found
 */
function getCategoryById($categoryId) {
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
        error_log("Error fetching category: " . $e->getMessage());
        return null;
    }
}

/**
 * Create a new category
 * @param string $name Category name
 * @param string $color Bootstrap color class
 * @return int|false Category ID on success, false on failure
 */
function createCategory($name, $color) {
    global $pdo;
    try {
        $stmt = $pdo->prepare("
            INSERT INTO categories (name, color, created_at, updated_at)
            VALUES (?, ?, NOW(), NOW())
        ");
        $stmt->execute([$name, $color]);
        return $pdo->lastInsertId();
    } catch (Exception $e) {
        error_log("Error creating category: " . $e->getMessage());
        return false;
    }
}

/**
 * Update a category
 * @param int $categoryId Category ID
 * @param string $name Category name
 * @param string $color Bootstrap color class
 * @return bool Success status
 */
function updateCategory($categoryId, $name, $color) {
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
        error_log("Error updating category: " . $e->getMessage());
        return false;
    }
}

/**
 * Delete a category
 * @param int $categoryId Category ID
 * @return bool Success status
 */
function deleteCategory($categoryId) {
    global $pdo;
    try {
        // Check if category is in use
        if (isCategoryInUse($categoryId)) {
            return false;
        }
        
        $stmt = $pdo->prepare("DELETE FROM categories WHERE id = ?");
        $stmt->execute([$categoryId]);
        return true;
    } catch (Exception $e) {
        error_log("Error deleting category: " . $e->getMessage());
        return false;
    }
}

/**
 * Check if a category is in use by any module
 * @param int $categoryId Category ID
 * @return bool True if in use, false otherwise
 */
function isCategoryInUse($categoryId) {
    global $pdo;
    try {
        // Check ideas
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM ideas WHERE category_id = ?");
        $stmt->execute([$categoryId]);
        if ($stmt->fetchColumn() > 0) {
            return true;
        }
        
        // Check work_documents
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM work_documents WHERE category_id = ?");
        $stmt->execute([$categoryId]);
        if ($stmt->fetchColumn() > 0) {
            return true;
        }
        
        return false;
    } catch (Exception $e) {
        error_log("Error checking category usage: " . $e->getMessage());
        return true; // Assume in use on error to prevent accidental deletion
    }
}

/**
 * Search categories by name
 * @param string $searchTerm Search term
 * @return array Array of matching categories
 */
function searchCategories($searchTerm) {
    global $pdo;
    try {
        $stmt = $pdo->prepare("
            SELECT id, name, color, created_at, updated_at
            FROM categories
            WHERE name LIKE ?
            ORDER BY name ASC
        ");
        $stmt->execute(['%' . $searchTerm . '%']);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        error_log("Error searching categories: " . $e->getMessage());
        return [];
    }
}

/**
 * Handle all POST/AJAX requests for categories
 * @return void Outputs JSON and exits if POST request
 */
function handleCategoryRequest() {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['action'])) {
        return; // Not a POST request, continue to page rendering
    }
    
    header('Content-Type: application/json');
    
    switch ($_POST['action']) {
        case 'create':
            $name = $_POST['name'] ?? '';
            $color = $_POST['color'] ?? 'secondary';
            
            $categoryId = createCategory($name, $color);
            if ($categoryId) {
                echo json_encode(['success' => true, 'id' => $categoryId, 'message' => 'Category created successfully']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to create category']);
            }
            break;
            
        case 'update':
            $id = $_POST['id'] ?? 0;
            $name = $_POST['name'] ?? '';
            $color = $_POST['color'] ?? 'secondary';
            
            if (updateCategory($id, $name, $color)) {
                echo json_encode(['success' => true, 'message' => 'Category updated successfully']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to update category']);
            }
            break;
            
        case 'delete':
            $id = $_POST['id'] ?? 0;
            if (deleteCategory($id)) {
                echo json_encode(['success' => true, 'message' => 'Category deleted successfully']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Cannot delete category - it is in use']);
            }
            break;
            
        case 'get_category':
            $id = $_POST['id'] ?? 0;
            $category = getCategoryById($id);
            if ($category) {
                echo json_encode(['success' => true, 'category' => $category]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Category not found']);
            }
            break;
            
        case 'search':
            $searchTerm = $_POST['search'] ?? '';
            $categories = searchCategories($searchTerm);
            echo json_encode(['success' => true, 'categories' => $categories]);
            break;
            
        default:
            echo json_encode(['success' => false, 'message' => 'Invalid action']);
    }
    
    exit;
}
