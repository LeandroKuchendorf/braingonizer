<?php
/**
 * Notes Helper Functions
 * Backend utilities for managing notes in Braingonizer
 */

require_once __DIR__ . '/../../db_connection.php';
require_once __DIR__ . '/../../../includes/global_functions.php';
require_once __DIR__ . '/tag_helpers.php';

/**
 * Handle all POST/AJAX requests for notes
 * @return void Outputs JSON and exits if POST request
 */
function handleNoteRequest() {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['action'])) {
        return; // Not a POST request, continue to page rendering
    }
    
    // Only handle requests explicitly meant for notes
    if (!isset($_POST['type']) || $_POST['type'] !== 'note') {
        return;
    }
    
    header('Content-Type: application/json');
    
    switch ($_POST['action']) {
        case 'create':
            $title = $_POST['title'] ?? '';
            $content = $_POST['content'] ?? '';
            $tagNames = isset($_POST['tags']) ? json_decode($_POST['tags'], true) : [];
            
            $noteId = createNote($title, $content, $tagNames);
            if ($noteId) {
                echo json_encode(['success' => true, 'id' => $noteId, 'message' => 'Note created successfully']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to create note']);
            }
            break;
            
        case 'update':
            $id = $_POST['id'] ?? 0;
            $title = $_POST['title'] ?? '';
            $content = $_POST['content'] ?? '';
            $tagNames = isset($_POST['tags']) ? json_decode($_POST['tags'], true) : [];
            
            if (updateNote($id, $title, $content, $tagNames)) {
                echo json_encode(['success' => true, 'message' => 'Note updated successfully']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to update note']);
            }
            break;
            
        case 'delete':
            $id = $_POST['id'] ?? 0;
            if (!$id) {
                echo json_encode(['success' => false, 'message' => 'Invalid note ID']);
                break;
            }
            
            $result = deleteNote($id);
            if ($result) {
                echo json_encode(['success' => true, 'message' => 'Note deleted successfully']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Unable to delete note. It may not exist or there was a database error.']);
            }
            break;
            
        case 'get_tags':
            $tags = getAllTags();
            echo json_encode(['success' => true, 'tags' => $tags]);
            break;
            
        case 'get_note':
            $id = $_POST['id'] ?? 0;
            $note = getNoteById($id);
            if ($note) {
                echo json_encode(['success' => true, 'note' => $note]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Note not found']);
            }
            break;
    }
    
    exit;
}

/**
 * Get all notes
 * @return array Array of all notes with their tags
 */
function getAllNotes() {
    $notes = get_sql("SELECT * FROM notes ORDER BY updated_at DESC");
    
    // Attach tags to each note
    foreach ($notes as &$note) {
        $note['tags'] = getItemTags('note', $note['id']);
    }
    
    return $notes;
}

/**
 * Get a single note by ID
 * @param int $id Note ID
 * @return array|null Note data with tags or null if not found
 */
function getNoteById($id) {
    global $pdo;
    try {
        $stmt = $pdo->prepare("SELECT * FROM notes WHERE id = ?");
        $stmt->execute([$id]);
        $note = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($note) {
            $note['tags'] = getItemTags('note', $note['id']);
        }
        
        return $note;
    } catch (PDOException $e) {
        error_log("Error fetching note: " . $e->getMessage());
        return null;
    }
}

/**
 * Create a new note
 * @param string $title Note title
 * @param string $content Note content
 * @param array $tagNames Array of tag names (max 3)
 * @return int|false Note ID on success, false on failure
 */
function createNote($title, $content, $tagNames = []) {
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
        
        // Create note with tags
        $stmt = $pdo->prepare("INSERT INTO notes (title, content, tags) VALUES (?, ?, ?)");
        $stmt->execute([$title, $content, $tagsString]);
        return $pdo->lastInsertId();
    } catch (PDOException $e) {
        error_log("Error creating note: " . $e->getMessage());
        return false;
    }
}

/**
 * Update an existing note
 * @param int $id Note ID
 * @param string $title Note title
 * @param string $content Note content
 * @param array $tagNames Array of tag names (max 3)
 * @return bool Success status
 */
function updateNote($id, $title, $content, $tagNames = []) {
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
        
        // Update note with tags
        $stmt = $pdo->prepare("UPDATE notes SET title = ?, content = ?, tags = ?, updated_at = NOW() WHERE id = ?");
        return $stmt->execute([$title, $content, $tagsString, $id]);
    } catch (PDOException $e) {
        error_log("Error updating note: " . $e->getMessage());
        return false;
    }
}

/**
 * Delete a note
 * @param int $id Note ID
 * @return bool Success status
 */
function deleteNote($id) {
    $result = run_sql("DELETE FROM notes WHERE id = ?", [$id]);
    return $result !== false;
}
