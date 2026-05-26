<?php
/**
 * Links Helper Functions
 * Handles all database operations and POST request handling for links
 */

require_once __DIR__ . '/../../db_connection.php';
require_once __DIR__ . '/../../../includes/global_functions.php';

/**
 * Get all link categories
 * @return array Array of all categories with their links
 */
function getAllCategories() {
    global $pdo;
    try {
        $stmt = $pdo->query("
            SELECT id, name, icon, color, created_at, updated_at
            FROM link_categories
            ORDER BY created_at DESC
        ");
        $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Get links for each category
        foreach ($categories as &$category) {
            $category['links'] = getCategoryLinks($category['id']);
        }
        
        return $categories;
    } catch (Exception $e) {
        error_log("Error fetching categories: " . $e->getMessage());
        return [];
    }
}

/**
 * Get category by ID
 * @param int $categoryId Category ID
 * @return array|null Category data with links or null if not found
 */
function getCategoryById($categoryId) {
    global $pdo;
    try {
        $stmt = $pdo->prepare("
            SELECT id, name, icon, color, created_at, updated_at
            FROM link_categories
            WHERE id = ?
        ");
        $stmt->execute([$categoryId]);
        $category = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($category) {
            $category['links'] = getCategoryLinks($categoryId);
        }
        
        return $category;
    } catch (Exception $e) {
        error_log("Error fetching category: " . $e->getMessage());
        return null;
    }
}

/**
 * Get all links for a category
 * @param int $categoryId Category ID
 * @return array Array of links
 */
function getCategoryLinks($categoryId) {
    global $pdo;
    try {
        $stmt = $pdo->prepare("
            SELECT id, title, url, category_id, created_at, updated_at
            FROM links
            WHERE category_id = ?
            ORDER BY created_at DESC
        ");
        $stmt->execute([$categoryId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        error_log("Error fetching category links: " . $e->getMessage());
        return [];
    }
}

/**
 * Create a new link category
 * @param string $name Category name
 * @param string $icon Font Awesome icon class
 * @param string $color Color theme
 * @return int|false Category ID on success, false on failure
 */
function createCategory($name, $icon, $color) {
    global $pdo;
    try {
        $stmt = $pdo->prepare("
            INSERT INTO link_categories (name, icon, color, created_at, updated_at)
            VALUES (?, ?, ?, NOW(), NOW())
        ");
        $stmt->execute([$name, $icon, $color]);
        return $pdo->lastInsertId();
    } catch (Exception $e) {
        error_log("Error creating category: " . $e->getMessage());
        return false;
    }
}

/**
 * Update a link category
 * @param int $categoryId Category ID
 * @param string $name Category name
 * @param string $icon Font Awesome icon class
 * @param string $color Color theme
 * @return bool Success status
 */
function updateCategory($categoryId, $name, $icon, $color) {
    global $pdo;
    try {
        $stmt = $pdo->prepare("
            UPDATE link_categories
            SET name = ?, icon = ?, color = ?, updated_at = NOW()
            WHERE id = ?
        ");
        $stmt->execute([$name, $icon, $color, $categoryId]);
        return true;
    } catch (Exception $e) {
        error_log("Error updating category: " . $e->getMessage());
        return false;
    }
}

/**
 * Delete a link category
 * @param int $categoryId Category ID
 * @return bool Success status
 */
function deleteCategory($categoryId) {
    global $pdo;
    try {
        // Delete all links in category first
        $stmt = $pdo->prepare("DELETE FROM links WHERE category_id = ?");
        $stmt->execute([$categoryId]);
        
        // Delete category
        $stmt = $pdo->prepare("DELETE FROM link_categories WHERE id = ?");
        $stmt->execute([$categoryId]);
        
        return true;
    } catch (Exception $e) {
        error_log("Error deleting category: " . $e->getMessage());
        return false;
    }
}

/**
 * Create a new link
 * @param string $title Link title
 * @param string $url Link URL
 * @param int $categoryId Category ID
 * @return int|false Link ID on success, false on failure
 */
function createLink($title, $url, $categoryId) {
    global $pdo;
    try {
        $stmt = $pdo->prepare("
            INSERT INTO links (title, url, category_id, created_at, updated_at)
            VALUES (?, ?, ?, NOW(), NOW())
        ");
        $stmt->execute([$title, $url, $categoryId]);
        return $pdo->lastInsertId();
    } catch (Exception $e) {
        error_log("Error creating link: " . $e->getMessage());
        return false;
    }
}

/**
 * Get link by ID
 * @param int $linkId Link ID
 * @return array|null Link data or null if not found
 */
function getLinkById($linkId) {
    global $pdo;
    try {
        $stmt = $pdo->prepare("
            SELECT id, title, url, category_id, created_at, updated_at
            FROM links
            WHERE id = ?
        ");
        $stmt->execute([$linkId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        error_log("Error fetching link: " . $e->getMessage());
        return null;
    }
}

/**
 * Update a link
 * @param int $linkId Link ID
 * @param string $title Link title
 * @param string $url Link URL
 * @param int $categoryId Category ID
 * @return bool Success status
 */
function updateLink($linkId, $title, $url, $categoryId) {
    global $pdo;
    try {
        $stmt = $pdo->prepare("
            UPDATE links
            SET title = ?, url = ?, category_id = ?, updated_at = NOW()
            WHERE id = ?
        ");
        $stmt->execute([$title, $url, $categoryId, $linkId]);
        return true;
    } catch (Exception $e) {
        error_log("Error updating link: " . $e->getMessage());
        return false;
    }
}

/**
 * Delete a link
 * @param int $linkId Link ID
 * @return bool Success status
 */
function deleteLink($linkId) {
    $result = run_sql("DELETE FROM links WHERE id = ?", [$linkId]);
    return $result !== false;
}

/**
 * Handle all POST/AJAX requests for links
 * @return void Outputs JSON and exits if POST request
 */
function handleLinkRequest() {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['action'])) {
        return; // Not a POST request, continue to page rendering
    }
    
    header('Content-Type: application/json');
    
    switch ($_POST['action']) {
        case 'create_category':
            $name = $_POST['name'] ?? '';
            $icon = $_POST['icon'] ?? '';
            $color = $_POST['color'] ?? 'generic';
            
            $categoryId = createCategory($name, $icon, $color);
            if ($categoryId) {
                echo json_encode(['success' => true, 'id' => $categoryId, 'message' => 'Category created successfully']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to create category']);
            }
            break;
            
        case 'update_category':
            $id = $_POST['id'] ?? 0;
            $name = $_POST['name'] ?? '';
            $icon = $_POST['icon'] ?? '';
            $color = $_POST['color'] ?? 'generic';
            
            if (updateCategory($id, $name, $icon, $color)) {
                echo json_encode(['success' => true, 'message' => 'Category updated successfully']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to update category']);
            }
            break;
            
        case 'delete_category':
            $id = $_POST['id'] ?? 0;
            if (deleteCategory($id)) {
                echo json_encode(['success' => true, 'message' => 'Category deleted successfully']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to delete category']);
            }
            break;
            
        case 'create_link':
            $title = $_POST['title'] ?? '';
            $url = $_POST['url'] ?? '';
            $categoryId = $_POST['category_id'] ?? 0;
            
            $linkId = createLink($title, $url, $categoryId);
            if ($linkId) {
                echo json_encode(['success' => true, 'id' => $linkId, 'message' => 'Link created successfully']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to create link']);
            }
            break;
            
        case 'update_link':
            $id = $_POST['id'] ?? 0;
            $title = $_POST['title'] ?? '';
            $url = $_POST['url'] ?? '';
            $categoryId = $_POST['category_id'] ?? 0;
            
            if (updateLink($id, $title, $url, $categoryId)) {
                echo json_encode(['success' => true, 'message' => 'Link updated successfully']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to update link']);
            }
            break;
            
        case 'delete_link':
            $id = $_POST['id'] ?? 0;
            if (!$id) {
                echo json_encode(['success' => false, 'message' => 'Invalid link ID']);
                break;
            }
            
            $result = deleteLink($id);
            if ($result) {
                echo json_encode(['success' => true, 'message' => 'Link deleted successfully']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Unable to delete link. It may not exist or there was a database error.']);
            }
            break;
            
        case 'get_link':
            $id = $_POST['id'] ?? 0;
            $link = getLinkById($id);
            if ($link) {
                echo json_encode(['success' => true, 'link' => $link]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Link not found']);
            }
            break;
    }
    
    exit;
}
