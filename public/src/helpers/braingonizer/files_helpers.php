<?php
/**
 * Files Helper Functions
 * Handles all database operations and POST request handling for files
 */

require_once __DIR__ . '/../../db_connection.php';
require_once __DIR__ . '/../../../includes/global_functions.php';
require_once __DIR__ . '/tag_helpers.php';

/**
 * Get all files
 * @return array Array of all files
 */
function getAllFiles() {
    global $pdo;
    try {
        $stmt = $pdo->query("
            SELECT f.id, f.name, f.description, f.category_id, f.file_path, f.file_size, 
                   f.mime_type, f.tags, f.created_at, f.updated_at,
                   c.name as category_name, c.color as category_color
            FROM files f
            LEFT JOIN categories c ON f.category_id = c.id
            ORDER BY f.created_at DESC
        ");
        $files = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Get tags for each file
        foreach ($files as &$file) {
            $file['tags'] = getItemTags('file', $file['id']);
        }
        
        return $files;
    } catch (Exception $e) {
        error_log("Error fetching files: " . $e->getMessage());
        return [];
    }
}

/**
 * Get file by ID
 * @param int $fileId File ID
 * @return array|null File data with tags or null if not found
 */
function getFileById($fileId) {
    global $pdo;
    try {
        $stmt = $pdo->prepare("
            SELECT f.id, f.name, f.description, f.category_id, f.file_path, f.file_size, 
                   f.mime_type, f.tags, f.created_at, f.updated_at,
                   c.name as category_name, c.color as category_color
            FROM files f
            LEFT JOIN categories c ON f.category_id = c.id
            WHERE f.id = ?
        ");
        $stmt->execute([$fileId]);
        $file = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($file) {
            $file['tags'] = getItemTags('file', $fileId);
        }
        
        return $file;
    } catch (Exception $e) {
        error_log("Error fetching file: " . $e->getMessage());
        return null;
    }
}

/**
 * Create a new file record
 * @param string $name File name
 * @param string $description File description
 * @param int|null $categoryId Category ID (foreign key to categories table)
 * @param string $filePath Path to uploaded file
 * @param int $fileSize File size in bytes
 * @param string $mimeType MIME type
 * @param array $tagNames Array of tag names
 * @return int|false File ID on success, false on failure
 */
function createFile($name, $description, $categoryId, $filePath, $fileSize, $mimeType, $tagNames = []) {
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
            INSERT INTO files (name, description, category_id, file_path, file_size, mime_type, tags, created_at, updated_at)
            VALUES (?, ?, ?, ?, ?, ?, ?, NOW(), NOW())
        ");
        $stmt->execute([$name, $description, $categoryId, $filePath, $fileSize, $mimeType, $tagsString]);
        return $pdo->lastInsertId();
    } catch (Exception $e) {
        error_log("Error creating file: " . $e->getMessage());
        return false;
    }
}

/**
 * Update a file record
 * @param int $fileId File ID
 * @param string $name File name
 * @param string $description File description
 * @param int|null $categoryId Category ID (foreign key to categories table)
 * @param array $tagNames Array of tag names
 * @return bool Success status
 */
function updateFile($fileId, $name, $description, $categoryId, $tagNames = []) {
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
            UPDATE files
            SET name = ?, description = ?, category_id = ?, tags = ?, updated_at = NOW()
            WHERE id = ?
        ");
        return $stmt->execute([$name, $description, $categoryId, $tagsString, $fileId]);
    } catch (Exception $e) {
        error_log("Error updating file: " . $e->getMessage());
        return false;
    }
}

/**
 * Delete a file
 * @param int $fileId File ID
 * @return bool Success status
 */
function deleteFile($fileId) {
    try {
        $file = getFileById($fileId);
        
        // Delete physical file from file system
        if ($file && file_exists($file['file_path'])) {
            unlink($file['file_path']);
        }
        
        // Delete database record using run_sql
        $result = run_sql("DELETE FROM files WHERE id = ?", [$fileId]);
        return $result !== false;
    } catch (Exception $e) {
        error_log("Error deleting file: " . $e->getMessage());
        return false;
    }
}


/**
 * Handle all POST/AJAX requests for files
 * @return void Outputs JSON and exits if POST request
 */
function handleFileRequest() {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['action'])) {
        return; // Not a POST request, continue to page rendering
    }
    
    header('Content-Type: application/json');
    
    switch ($_POST['action']) {
        case 'upload':
            // File upload is typically handled separately with multipart/form-data
            // This is a placeholder for future implementation
            echo json_encode(['success' => false, 'message' => 'File upload not yet implemented']);
            break;
            
        case 'update':
            $id = $_POST['id'] ?? 0;
            $name = $_POST['name'] ?? '';
            $description = $_POST['description'] ?? '';
            $categoryId = $_POST['category_id'] ?? null;
            $tagNames = isset($_POST['tags']) ? json_decode($_POST['tags'], true) : [];
            
            if (updateFile($id, $name, $description, $categoryId, $tagNames)) {
                echo json_encode(['success' => true, 'message' => 'File updated successfully']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to update file']);
            }
            break;
            
        case 'delete':
            $id = $_POST['id'] ?? 0;
            if (!$id) {
                echo json_encode(['success' => false, 'message' => 'Invalid file ID']);
                break;
            }
            
            $result = deleteFile($id);
            if ($result) {
                echo json_encode(['success' => true, 'message' => 'File deleted successfully']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Unable to delete file. It may not exist or there was a database error.']);
            }
            break;
            
        case 'get_file':
            $id = $_POST['id'] ?? 0;
            $file = getFileById($id);
            if ($file) {
                echo json_encode(['success' => true, 'file' => $file]);
            } else {
                echo json_encode(['success' => false, 'message' => 'File not found']);
            }
            break;
    }
    
    exit;
}
