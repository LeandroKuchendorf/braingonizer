<?php
/**
 * Documents Helper Functions
 * Backend utilities for managing work documents in Workonizer
 */

require_once __DIR__ . '/../../db_connection.php';
require_once __DIR__ . '/../../../includes/global_functions.php';
require_once __DIR__ . '/../braingonizer/tag_helpers.php';

/**
 * Handle all POST/AJAX requests for documents
 * @return void Outputs JSON and exits if POST request
 */
function handleDocumentRequest() {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['action'])) {
        return; // Not a POST request, continue to page rendering
    }
    
    header('Content-Type: application/json');
    
    switch ($_POST['action']) {
        case 'create':
            $title = $_POST['title'] ?? '';
            $content = $_POST['content'] ?? '';
            $category_id = $_POST['category_id'] ?? null;
            $tagNames = isset($_POST['tags']) ? json_decode($_POST['tags'], true) : [];
            
            $documentId = createDocument($title, $content, $category_id, $tagNames);
            if ($documentId) {
                echo json_encode(['success' => true, 'id' => $documentId, 'message' => 'Document created successfully']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to create document']);
            }
            break;
            
        case 'update':
            $id = $_POST['id'] ?? 0;
            $title = $_POST['title'] ?? '';
            $content = $_POST['content'] ?? '';
            $category_id = $_POST['category_id'] ?? null;
            $tagNames = isset($_POST['tags']) ? json_decode($_POST['tags'], true) : [];
            
            if (updateDocument($id, $title, $content, $category_id, $tagNames)) {
                echo json_encode(['success' => true, 'message' => 'Document updated successfully']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to update document']);
            }
            break;
            
        case 'delete':
            $id = $_POST['id'] ?? 0;
            if (!$id) {
                echo json_encode(['success' => false, 'message' => 'Invalid document ID']);
                break;
            }
            
            $result = deleteDocument($id);
            if ($result) {
                echo json_encode(['success' => true, 'message' => 'Document deleted successfully']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Unable to delete document. It may not exist or there was a database error.']);
            }
            break;
            
        case 'get_document':
            $id = $_POST['id'] ?? 0;
            $document = getDocumentById($id);
            if ($document) {
                echo json_encode(['success' => true, 'document' => $document]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Document not found']);
            }
            break;
            
        case 'get_tags':
            $tags = getAllTags();
            echo json_encode(['success' => true, 'tags' => $tags]);
            break;
    }
    
    exit;
}

/**
 * Get all documents
 * @return array Array of all documents with their tags
 */
function getAllDocuments() {
    $documents = get_sql("SELECT * FROM work_documents ORDER BY updated_at DESC");
    
    // Attach tags to each document
    foreach ($documents as &$document) {
        $document['tags'] = getItemTags('work_document', $document['id']);
    }
    
    return $documents;
}

/**
 * Get a single document by ID
 * @param int $id Document ID
 * @return array|null Document data with tags or null if not found
 */
function getDocumentById($id) {
    global $pdo;
    try {
        $stmt = $pdo->prepare("SELECT * FROM work_documents WHERE id = ?");
        $stmt->execute([$id]);
        $document = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($document) {
            $document['tags'] = getItemTags('work_document', $document['id']);
        }
        
        return $document;
    } catch (PDOException $e) {
        error_log("Error fetching document: " . $e->getMessage());
        return null;
    }
}

/**
 * Create a new document
 * @param string $title Document title
 * @param string $content Document content
 * @param int|null $category_id Category ID
 * @param array $tagNames Array of tag names
 * @return int|false Document ID on success, false on failure
 */
function createDocument($title, $content, $category_id = null, $tagNames = []) {
    global $pdo;
    try {
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
        
        // Create document with tags
        $stmt = $pdo->prepare("INSERT INTO work_documents (title, content, category_id, tags) VALUES (?, ?, ?, ?)");
        $stmt->execute([$title, $content, $category_id, $tagsString]);
        return $pdo->lastInsertId();
    } catch (PDOException $e) {
        error_log("Error creating document: " . $e->getMessage());
        return false;
    }
}

/**
 * Update an existing document
 * @param int $id Document ID
 * @param string $title Document title
 * @param string $content Document content
 * @param int|null $category_id Category ID
 * @param array $tagNames Array of tag names
 * @return bool Success status
 */
function updateDocument($id, $title, $content, $category_id = null, $tagNames = []) {
    global $pdo;
    try {
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
        
        // Update document with tags
        $stmt = $pdo->prepare("UPDATE work_documents SET title = ?, content = ?, category_id = ?, tags = ?, updated_at = NOW() WHERE id = ?");
        return $stmt->execute([$title, $content, $category_id, $tagsString, $id]);
    } catch (PDOException $e) {
        error_log("Error updating document: " . $e->getMessage());
        return false;
    }
}

/**
 * Delete a document
 * @param int $id Document ID
 * @return bool Success status
 */
function deleteDocument($id) {
    $result = run_sql("DELETE FROM work_documents WHERE id = ?", [$id]);
    return $result !== false;
}
