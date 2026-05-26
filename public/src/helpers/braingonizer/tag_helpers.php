<?php
/**
 * Tag Helper Functions
 * Global tag management utilities for Braingonizer
 */

require_once __DIR__ . '/../../db_connection.php';
require_once __DIR__ . '/../../../includes/global_functions.php';

/**
 * Handle all POST/AJAX requests for tags
 * @return void Outputs JSON and exits if POST request
 */
function handleTagRequest() {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['action'])) {
        return; // Not a POST request, continue to page rendering
    }
    
    header('Content-Type: application/json');
    
    switch ($_POST['action']) {
        case 'create':
            $name = $_POST['name'] ?? '';
            $color = $_POST['color'] ?? null;
            $tagId = createTag($name, $color);
            if ($tagId) {
                echo json_encode(['success' => true, 'id' => $tagId, 'message' => 'Tag created successfully']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to create tag or tag already exists']);
            }
            break;
            
        case 'update':
            $id = $_POST['id'] ?? 0;
            $name = $_POST['name'] ?? '';
            $color = $_POST['color'] ?? '';
            if (updateTag($id, $name, $color)) {
                echo json_encode(['success' => true, 'message' => 'Tag updated successfully']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to update tag']);
            }
            break;
            
        case 'delete':
            $id = $_POST['id'] ?? 0;
            if (!canDeleteTag($id)) {
                $usageCount = getTagUsageCount($id);
                echo json_encode([
                    'success' => false, 
                    'message' => "This tag is currently being used in {$usageCount} item(s) and cannot be deleted. Please remove it from all items first."
                ]);
            } else {
                if (deleteTag($id)) {
                    echo json_encode(['success' => true, 'message' => 'Tag deleted successfully']);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Failed to delete tag']);
                }
            }
            break;
            
        case 'search':
            $query = $_POST['query'] ?? '';
            $tags = searchTags($query);
            echo json_encode(['success' => true, 'tags' => $tags]);
            break;
    }
    
    exit;
}

/**
 * Get all tags from database
 * @return array Array of all tags
 */
function getAllTags() {
    return get_sql("SELECT * FROM tags ORDER BY name ASC");
}

/**
 * Get a single tag by ID
 * @param int $id Tag ID
 * @return array|null Tag data or null if not found
 */
function getTagById($id) {
    global $pdo;
    try {
        $stmt = $pdo->prepare("SELECT * FROM tags WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Error fetching tag by ID: " . $e->getMessage());
        return null;
    }
}

/**
 * Get a tag by name (case-insensitive)
 * @param string $name Tag name
 * @return array|null Tag data or null if not found
 */
function getTagByName($name) {
    global $pdo;
    try {
        $stmt = $pdo->prepare("SELECT * FROM tags WHERE LOWER(name) = LOWER(?)");
        $stmt->execute([trim($name)]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Error fetching tag by name: " . $e->getMessage());
        return null;
    }
}

/**
 * Get random color from predefined palette
 * @return string Bootstrap color class name
 */
function getRandomColor() {
    $colors = ['primary', 'secondary', 'success', 'danger', 'warning', 'info'];
    return $colors[array_rand($colors)];
}

/**
 * Create a new tag
 * @param string $name Tag name
 * @param string|null $color Bootstrap color class (if null, random color assigned)
 * @return int|false Tag ID on success, false on failure
 */
function createTag($name, $color = null) {
    global $pdo;
    try {
        $name = trim($name);
        if (empty($name)) {
            return false;
        }
        
        // Check if tag already exists
        $existing = getTagByName($name);
        if ($existing) {
            return $existing['id'];
        }
        
        // Assign random color if not provided
        if ($color === null) {
            $color = getRandomColor();
        }
        
        $stmt = $pdo->prepare("INSERT INTO tags (name, color) VALUES (?, ?)");
        $stmt->execute([$name, $color]);
        return $pdo->lastInsertId();
    } catch (PDOException $e) {
        error_log("Error creating tag: " . $e->getMessage());
        return false;
    }
}

/**
 * Update an existing tag
 * @param int $id Tag ID
 * @param string $name New tag name
 * @param string $color New tag color
 * @return bool Success status
 */
function updateTag($id, $name, $color) {
    global $pdo;
    try {
        $name = trim($name);
        if (empty($name)) {
            return false;
        }
        
        $stmt = $pdo->prepare("UPDATE tags SET name = ?, color = ? WHERE id = ?");
        return $stmt->execute([$name, $color, $id]);
    } catch (PDOException $e) {
        error_log("Error updating tag: " . $e->getMessage());
        return false;
    }
}

/**
 * Check if a tag can be deleted (not in use)
 * @param int $tagId Tag ID
 * @return bool True if tag can be deleted, false if in use
 */
function canDeleteTag($tagId) {
    global $pdo;
    
    // Tables that have a 'tags' column with comma-separated IDs
    $tables = [
        'reminders',
        'notes',
        'tasks',
        'ideas',
        'files',
        'researches',
        'links'
    ];
    
    try {
        foreach ($tables as $table) {
            // Skip tables that don't exist yet
            $check = $pdo->query("SHOW TABLES LIKE '{$table}'");
            if ($check->rowCount() === 0) {
                continue;
            }
            
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM {$table} WHERE FIND_IN_SET(?, tags) > 0");
            $stmt->execute([$tagId]);
            $count = $stmt->fetchColumn();
            
            if ($count > 0) {
                return false; // Tag is in use
            }
        }
        return true; // Safe to delete
    } catch (PDOException $e) {
        error_log("Error checking tag deletion safety: " . $e->getMessage());
        return false;
    }
}

/**
 * Delete a tag (only if not in use)
 * @param int $id Tag ID
 * @return bool Success status
 */
function deleteTag($id) {
    // Check if tag can be deleted
    if (!canDeleteTag($id)) {
        return false;
    }
    
    $result = run_sql("DELETE FROM tags WHERE id = ?", [$id]);
    return $result !== false;
}

/**
 * Get existing tag or create new one
 * @param string $name Tag name
 * @return int|false Tag ID on success, false on failure
 */
function getOrCreateTag($name) {
    $existing = getTagByName($name);
    if ($existing) {
        return $existing['id'];
    }
    return createTag($name);
}

/**
 * Get all tags for a specific item
 * @param string $itemType Type of item (reminder, note, task, idea, file, research, link)
 * @param int $itemId Item ID
 * @return array Array of tags
 */
function getItemTags($itemType, $itemId) {
    global $pdo;
    try {
        $tableName = $itemType . 's'; // reminders, notes, tasks, etc.
        
        // Get the tags column value
        $stmt = $pdo->prepare("SELECT tags FROM {$tableName} WHERE id = ?");
        $stmt->execute([$itemId]);
        $tagIds = $stmt->fetchColumn();
        
        if (empty($tagIds)) {
            return [];
        }
        
        // Convert comma-separated IDs to array
        $tagIdArray = array_map('intval', explode(',', $tagIds));
        
        // Fetch tag details
        $placeholders = implode(',', array_fill(0, count($tagIdArray), '?'));
        $stmt = $pdo->prepare("SELECT * FROM tags WHERE id IN ({$placeholders}) ORDER BY name ASC");
        $stmt->execute($tagIdArray);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Error fetching item tags: " . $e->getMessage());
        return [];
    }
}

/**
 * Assign tags to an item (replaces existing tags)
 * @param string $itemType Type of item (reminder, note, task, idea, file, research, link)
 * @param int $itemId Item ID
 * @param array $tagIds Array of tag IDs (max 3)
 * @return bool Success status
 */
function assignTagsToItem($itemType, $itemId, $tagIds) {
    global $pdo;
    try {
        // Validate max 3 tags
        if (count($tagIds) > 3) {
            return false;
        }
        
        $tableName = $itemType . 's'; // reminders, notes, tasks, etc.
        
        // Convert tag IDs array to comma-separated string
        $tagsString = !empty($tagIds) ? implode(',', array_map('intval', $tagIds)) : null;
        
        // Update the tags column
        $stmt = $pdo->prepare("UPDATE {$tableName} SET tags = ? WHERE id = ?");
        return $stmt->execute([$tagsString, $itemId]);
    } catch (PDOException $e) {
        error_log("Error assigning tags to item: " . $e->getMessage());
        return false;
    }
}

/**
 * Get usage count for a tag
 * @param int $tagId Tag ID
 * @return int Total usage count across all item types
 */
function getTagUsageCount($tagId) {
    global $pdo;
    
    $tables = [
        'reminders',
        'notes',
        'tasks',
        'ideas',
        'files',
        'researches',
        'links'
    ];
    
    $totalCount = 0;
    
    try {
        foreach ($tables as $table) {
            // Skip tables that don't exist yet
            $check = $pdo->query("SHOW TABLES LIKE '{$table}'");
            if ($check->rowCount() === 0) {
                continue;
            }
            
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM {$table} WHERE FIND_IN_SET(?, tags) > 0");
            $stmt->execute([$tagId]);
            $totalCount += $stmt->fetchColumn();
        }
        return $totalCount;
    } catch (PDOException $e) {
        error_log("Error getting tag usage count: " . $e->getMessage());
        return 0;
    }
}

/**
 * Search tags by name
 * @param string $query Search query
 * @return array Array of matching tags
 */
function searchTags($query) {
    global $pdo;
    try {
        $stmt = $pdo->prepare("SELECT * FROM tags WHERE name LIKE ? ORDER BY name ASC");
        $stmt->execute(['%' . $query . '%']);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Error searching tags: " . $e->getMessage());
        return [];
    }
}
