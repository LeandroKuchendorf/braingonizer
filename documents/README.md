# Braingonizer

A personal tool to organize the chaos in my brain — and a sandbox for learning new things.

## About

Braingonizer is a web-based personal organization system designed to help manage thoughts, ideas, files, and links in one place. This project serves dual purposes:
- **Personal productivity**: A custom-built tool to organize my brain and workflow
- **Learning sandbox**: An experimental playground for trying new technologies and techniques

The project is in early stages and evolving as needs arise.

## Features

- **Dashboard**: Central hub for quick access to all modules
- **Ideas Management**: Capture and organize thoughts and ideas
- **File Organization**: Manage and track files
- **Link Collection**: Save and categorize useful links
- **Notes System**: Create and maintain personal notes
- **Tasks**: Track to-dos and action items

## Tech Stack

This is a **vanilla web stack** — no frameworks like React or Next.js. Keeping it simple and classic:

- **PHP** — Backend logic and server-side rendering
- **JavaScript** — Client-side interactivity
- **CSS** — Styling (custom, no frameworks)
- **HTML** — Structure

Additional Python and JS libraries may be added as needed for specific features.

## Project Structure

```
braingonizer/
├── public/              # Web root (point your server here)
│   ├── index.php        # Landing/login page
│   ├── dashboard.php    # Main dashboard
│   ├── ideas.php        # Ideas management
│   ├── files.php        # File organization
│   ├── links.php        # Link collection
│   ├── notes.php        # Notes system
│   ├── tasks.php        # Task management
│   ├── includes/        # Reusable components
│   │   ├── global_header.php
│   │   ├── global_footer.php
│   │   └── global_sidebar.php
│   └── assets/
│       ├── css/         # Stylesheets
│       ├── js/          # JavaScript files
│       └── images/      # Image assets
├── src/                 # PHP backend code
│   ├── db_connection.php    # Database configuration
│   ├── global_functions.php # Core SQL utilities
│   ├── helpers.php          # General utility functions
│   └── helpers/             # Page-specific helpers
│       ├── notes_helpers.php
│       ├── reminder_helpers.php
│       └── tag_helpers.php
├── .gitignore
└── README.md
```

## Getting Started

1. Clone the repository
2. Point your web server (Apache, Nginx, etc.) to the `public/` directory
3. Ensure PHP is installed and configured
4. Access via your local server (e.g., `http://localhost/braingonizer`)

## Development Philosophy

- **Minimal dependencies**: Vanilla stack for maximum control and learning
- **Iterative development**: Features added as needed
- **Personal first**: Built for my workflow, but designed to be adaptable
- **Learning by doing**: Experimenting with different approaches and patterns
