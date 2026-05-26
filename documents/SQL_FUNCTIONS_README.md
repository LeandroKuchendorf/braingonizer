# SQL Functions Guide

## Overview

Two helper functions to simplify database queries by eliminating repetitive PDO boilerplate code.

## Functions

### `get_sql(string $sql): array`

**Purpose:** Execute SELECT queries and retrieve data

**Returns:** Array of results (empty array if no results or error occurs)

**Usage:**
```php
// Get all tags
$tags = get_sql("SELECT * FROM tags");

// Get active users
$users = get_sql("SELECT * FROM users WHERE active = 1");

// Get recent notes with limit
$notes = get_sql("SELECT * FROM notes ORDER BY created_at DESC LIMIT 10");
```

---

### `run_sql(string $sql): bool`

**Purpose:** Execute INSERT, UPDATE, and DELETE queries

**Returns:** `true` on success, `false` on failure

**Usage:**
```php
// INSERT
run_sql("INSERT INTO logs (message) VALUES ('Action completed')");

// UPDATE
run_sql("UPDATE tasks SET status = 'done' WHERE id = 5");

// DELETE
run_sql("DELETE FROM temp_data WHERE created_at < NOW() - INTERVAL 7 DAY");
```

---

## Important Security Warning

⚠️ **These functions are for SIMPLE, STATIC queries ONLY.**

**NEVER use these functions with user input** — you risk SQL injection attacks.

### ❌ UNSAFE (Do NOT do this):
```php
$userId = $_GET['id'];
$user = get_sql("SELECT * FROM users WHERE id = $userId");
// SQL INJECTION RISK!
```

### ✅ SAFE (Use prepared statements for user input):
```php
$userId = $_GET['id'];
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$userId]);
$user = $stmt->fetch();
// SAFE from SQL injection
```

---

## When to Use

**Use `get_sql()` and `run_sql()` for:**
- Hardcoded queries with no variables
- Quick database operations during development
- Simple admin tasks

**Use prepared statements for:**
- Any query with user input
- Queries with dynamic values
- Production code handling external data

---

## Error Handling

Both functions:
- Log errors automatically (check error logs)
- Never expose database errors to users
- Return safe defaults (`[]` or `false`)

---

## How to Include

Add to your PHP files:
```php
require_once __DIR__ . '/src/global_functions.php';
```
