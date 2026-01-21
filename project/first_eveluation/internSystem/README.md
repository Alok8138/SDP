# Intern Operations System

A comprehensive, frontend-only web application for managing intern operations, assigning tasks, and tracking progress. This system simulates a real-world application environment using local storage and a fake backend server.

## 🚀 Features

### 👤 Role-Based Access Control
- **HR Login**: Complete administrative access to manage interns, tasks, and system settings.
- **Intern Login**: Limited access focused on assigned tasks and personal progress.

### 📊 Dashboard
- Real-time statistics on interns and tasks.
- Quick actions for common operations.
- Recent activity logs.

### 👨‍🎓 Intern Management
- Detailed list of interns with filtering capabilities (Status, Skills).
- Add new interns with skill tagging.
- Monitor intern status (Onboarding, Active, Exited).

### 📋 Task Management
- Create and manage tasks with estimated hours and required skills.
- Support for task dependencies (Task A must be done before Task B).
- Task status tracking (Pending, Assigned, In Progress, Done).

### 🤝 Assignments
- Intelligent assignment system matching tasks to eligible interns.
- Validation based on skills and intern availability.

## 🛠️ Technology Stack

- **Frontend**: HTML5, CSS3, Vanilla JavaScript (ES6+)
- **State Management**: Custom centralized state management with LocalStorage persistence.
- **Simulation**: `fake-server.js` simulates async network requests and latency.
- **Styling**: Modular CSS (`layout.css`, `components.css`) with FontAwesome icons.

## 🚦 Getting Started

1.  **Clone the repository** (or download the files).
2.  **Open the application**: Simply open `index.html` in any modern web browser. No local server or build process is required.

## 🔐 Default Credentials

### HR (Admin) Access
- **User ID**: `hr123`
- **Password**: `hr@1234`

### Intern Access
- **User ID**: Use any Intern ID created in the system (e.g., `2026-0001`).
- **Password**: Set your own password upon first login.

## 📂 Project Structure

- `css/`: Stylesheets for layout and components.
- `js/`: Application logic.
    - `app.js`: Main application entry point.
    - `state.js`: Centralized state management.
    - `renderer.js`: UI rendering logic.
    - `rules-engine.js`: Business logic and validation rules.
    - `fake-server.js`: Async backend simulation.
    - `validators.js`: Input validation helpers.
- `index.html`: Main entry point.

## ⚠️ Note

This project runs entirely in the browser using `localStorage` for data persistence. Clearing your browser cache will reset the application data.

---
*Developed for Handling task Management.*
