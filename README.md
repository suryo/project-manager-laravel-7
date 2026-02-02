# Project Management & Ticketing System

A robust internal management platform built with Laravel 10, designed to streamline project workflows, task assignments, and department-level ticketing.

## 🚀 Key Features

### 📁 Project Management

- **Full Project Lifecycle**: Manage projects from initial planning to completion.
- **Budget Tracking**: Monitor project budgets and financial constraints.
- **Department Integration**: Organize projects by department for clear ownership.
- **POAC Tracking**: Integrated "Planning, Organizing, Actuating, Controlling" log system for every project.
- **Interactive Badges**: Real-time status visibility with a modern design.

### ✅ Advanced Task Management

- **Visual Status Workflow**: Tasks progress through `Todo`, `In Progress`, `Review`, `Test`, `Check`, and `Done`.
- **Dynamic Quick Updates**: Seamlessly update task status via a sleek modal interface with real-time AJAX feedback.
- **Assignment Logic**: Multi-user task assignment with clear due date tracking.
- **Interactive Comments**: Integrated rich-text commenting (Quill Editor) for task collaboration.
- **Admin/SPV Control**: Strict role-based validation for critical status changes (e.g., "Mark as Done").

### 🎫 Comprehensive Ticketing System

- **Request Management**: Internal and external ticketing for `New Features`, `Bug Fixes`, `Enhancements`, `Web`, and `Design`.
- **Stage Progression**: Multi-stage workflow including document preparation, approval, and implementation.
- **Document Management**: Upload, review, and approve critical documents linked to tickets.
- **Resource Monitor**: Integrated monthly "Energy Monitor" to track staff capacity and resource utilization.
- **SPV Visibility**: Department Supervisors can oversee all tickets within their assigned departments.

### 🏢 Department & Team Organization

- **Hierarchical Departments**: Support for parent/child department structures.
- **Role-Based Access (RBAC)**:
    - **Admin**: Full system control and cross-department visibility.
    - **SPV (Supervisor)**: Department-level management and oversight.
    - **Staff**: Focused task execution and collaboration.
    - **Client**: External request management and tracking.
- **Meeting Management**: Track department meetings and attendance records.

## 🛠 Technical Stack

- **Backend**: Laravel 10 (PHP 8.x)
- **Frontend**: Bootstrap 5.3, Vanilla CSS (Modern "Metronic-style" Design)
- **Database**: MySQL / MariaDB
- **UI Components**:
    - **Quill Editor**: For rich-text collaboration.
    - **Bootstrap Icons**: For consistent visual cues.
    - **AJAX Driven**: High-performance interaction (Modal Task View, Status Updates, Comments) without page reloads.

## 💻 Installation & Setup

1. **Clone the repository**:

    ```bash
    git clone [repository-url]
    cd project-manager-laravel-7
    ```

2. **Install dependencies**:

    ```bash
    composer install
    npm install
    ```

3. **Environment Setup**:

    ```bash
    cp .env.example .env
    php artisan key:generate
    ```

4. **Database Configuration**:
   Update your `.env` file with your database credentials.

5. **Run Migrations & Seeders**:

    ```bash
    php artisan migrate --seed
    ```

6. **Storage Link**:

    ```bash
    php artisan storage:link
    ```

7. **Start Server**:
    ```bash
    php artisan serve
    ```

## 🎨 Design Philosophy

The system prioritizes **Functional Elegance**. It uses a modern, fluid layout featuring soft shadows, refined typography (Inter/Outfit), and interactive elements designed for a premium user experience. The interface is fully responsive, ensuring productivity across all devices.

---

_Built for excellence in project and resource management._
