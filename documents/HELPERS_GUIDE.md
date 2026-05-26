# PHP Helpers Guide

## What Are Helpers?

Helpers are **reusable PHP functions** that contain common logic you'd otherwise repeat across multiple pages. They're utility files that centralize functionality.

## Why Use Them?

- **DRY Principle**: Don't Repeat Yourself. Write logic once, use it everywhere.
- **Maintainability**: Fix a bug in one place instead of hunting through 10 files.
- **Consistency**: Same function behavior across your entire app.
- **Organization**: Keeps pages clean and focused on presentation logic.

## When to Use Helpers

Create a helper when you have:
- Logic used by **2+ pages**
- Complex operations (database queries, calculations, validations)
- Utility functions (formatting, sanitizing, checking permissions)

## Your Current Helpers

| Helper | Purpose | Used By |
|--------|---------|---------|
| `db_connection.php` | Database connection setup | All pages |
| `helpers.php` | General utilities (formatting, validation) | Multiple pages |
| `reminder_helpers.php` | Reminder-specific functions | reminders.php |
| `tag_helpers.php` | Tag management functions | tags.php |

## Should Every Page Have a Helper?

**No.** Only create a helper if:
- The logic is **reused** across multiple pages, OR
- The logic is **complex** and deserves its own file for clarity

**Example:**
- ✅ `tag_helpers.php` → Used by tags.php and potentially other pages that need tag functionality
- ❌ Don't create a helper just for one page's unique logic

## Structure Pattern

```
public/
├── page.php          (presentation & routing)
└── src/
    └── page_helpers.php  (business logic)
```

**page.php** → Handles requests, includes helpers, renders HTML  
**page_helpers.php** → Contains all the functions that do the work

## Quick Example

**Bad** (logic in page):
```php
// In tasks.php
$stmt = $pdo->prepare("SELECT * FROM tasks WHERE user_id = ?");
$stmt->execute([$userId]);
$tasks = $stmt->fetchAll();
```

**Good** (logic in helper):
```php
// In task_helpers.php
function getUserTasks($userId) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM tasks WHERE user_id = ?");
    $stmt->execute([$userId]);
    return $stmt->fetchAll();
}

// In tasks.php
$tasks = getUserTasks($userId);
```

## Summary

- **Helpers** = reusable function libraries
- **Create one** when logic is used 2+ times or is complex
- **Not every page needs one** — only if there's shared logic
- **Keep pages clean** — move heavy lifting to helpers
