<div align="center">
<h1 style="margin-top: 0; padding: 0;">
   <img src="public/assets/icons/logo/logo-FFF.svg" alt="Haarlem Festival Logo" style="height: 48px; width: auto; vertical-align: middle; margin-bottom: 12px;">
   Haarlem Festival
</h1>
</div>

[//]: # (TODO: Verify README.md before delivering project)

[//]: # (TODO: Add screenshot of the website)
<!-- ![Edit Project View](docs/edit.png)) -->
![PHP](https://img.shields.io/badge/php-777BB4?style=for-the-badge&logo=php&logoColor=white)
![TypeScript](https://img.shields.io/badge/ts-3178C6.svg?style=for-the-badge&logo=typescript&logoColor=white)
![PostgreSQL](https://img.shields.io/badge/postgresql-4169E1?style=for-the-badge&logo=postgresql&logoColor=white)
![Docker](https://img.shields.io/badge/docker-0db7ed.svg?style=for-the-badge&logo=docker&logoColor=white)
![TailwindCSS](https://img.shields.io/badge/tailwindcss-38B2AC.svg?style=for-the-badge&logo=tailwind-css&logoColor=white)
![NGINX](https://img.shields.io/badge/nginx-009639.svg?style=for-the-badge&logo=nginx&logoColor=white)  
🎪 Festival showcase & ticket purchasing platform. 🐘 Built with PHP, JavaScript & PostgreSQL

# Running the Project

## Prerequisites

- **Docker CLI** & **Docker Compose** installed on your system
- **Node.js** & **npm** (for building assets)
- **Composer** (for PHP dependencies)

## Build and Run

1. Clone the repository.
2. Navigate to the project directory.
3. Build assets with npm:
   ```bash
   npm install
   npm run build
   ```
4. Install PHP dependencies with composer:
   ```bash
   composer install
   ```
5. Start the project:
   ```bash
   docker-compose up -d
   ```

## Usage

- Website: http://localhost/
- pgAdmin: http://localhost:8080, use credentials found in `compose.yml -> pgadmin`:
    - Site login:
        - **Email Address:** admin@local.dev
        - **Password:** admin123
    - Server connection (Project->HaarlemFestival:
        - **Password:** database123

<details>
<summary><b>Accessing Database Tables</b></summary>

To view table data in pgAdmin:

1. Navigate down through the tree: **Project** → **HaarlemFestival** → **Databases** → **HaarlemFestival** → **Schemas
   ** → **public** → **Tables**
2. Click to the table you want to view (e.g., `users`)
3. Click the **"All Rows"** button in the top toolbar to display all table data (middle section)

![Database Access](docs/db-access.png)

</details>

## Stop / Cleanup

* Stop containers:
   ```bash
   docker-compose stop
* Remove containers and named volume _(festival_postgres_data)_:
    ```bash
   docker-compose down -v

# Features

## Technologies Used

- Docker
- Nginx 1.29
- PHP: 8.5-fpm
    - composer: latest
      - psr-4
      - vlucas/phpdotenv: ^5
      - nikic/fast-route: ^1
      - phpmailer/phpmailer: ^7
      - stripe/stripe-php": ^19
    - npm
      - tailwindcss: ^4
- PostgreSQL: ^18
    - pgadmin: ^9

## Security

[//]: # (TODO: Verify `## Security` before delivering project )

- **Router Authentication**: All routes protected via Router's `AuthServ::requireAuthentication(...)` checking
  session auth status
- **Router Authorization**: All routes verify a user's `UserRole` against the routes `AccessRole` before allowing access
  through `AuthServ::requireAuthentication(...)` ([Router](/app/Routing/Router.php))
- **SQL Injection Prevention**: All database queries use a base PDO statement
- **XSS & CSP**: Randomly generated CSP nonces for all JS scripts, with CSP setup to be as strict as possible
- **CSRF Protection**: Randomly generated CSRF token validation for all POST requests via `Csrf` class
- **Input Validation & Sanitation**: Server-side validation of all user input & HTML sanitization via `Escaper` class
- **Password Security**: Passwords hashed with bcrypt (`password_hash()`)
- **Data Minimization**: DTOs (`UserAuthDto`, `UserIdentityDto`, etc.) limit data exposure
- **Error Handling**: Sensitive errors logged only on server-side, whilst users receive easy to understand messages

## MVC & Architecture

[//]: # (TODO: Verify `## MVC & Architecture` before delivering project)

- **Service & Repository Layers**: Business logic in services (`AuthServ`...), data access in repositories (
  `AuthRepo`...)
- **Interface-Based Integration**: All layers use interfaces (`*IAuthServ`, `*IUserRepo`)
- **Dependency Injection**: Constructor Dependency Injection for all backend layers (controllers, services,
  repositories)
- **Routing**: Route definitions in `Routes.php` with controller method binding via `Router` class
- **View Templating**: `View::render()` provides automatic view mapping and data passing to templates
- **DRY Principles**: Shared helpers eliminate code duplication:
    - `BaseServExc::handleRepoCall()` - Repository error handling across all services
    - `View::render()` - View rendering with automatic data extraction
    - `BaseApiCtrl` - Common API response formatting
    - `Routes::route()` - Additional fields for route authentication/authorization
- **OOP Concepts**:
    - Inheritance (exception hierarchy, base classes)
    - Encapsulation (private/protected properties, DTOs)
    - Polymorphism (interface implementations, enums)
- **final & readonly**: As many classes marked as `final` and/or `readonly` for immutability and clarity

## API & JS

[//]: # (TODO: Verify `## API & JS` before delivering project)

[//]: # (- **JSON API Endpoints**: `` provides REST API for task operations through AJAX &#40;create, edit, delete&#41;)

[//]: # (  with JSON responses)

- **Authentication & Authorization**: All API requests verify project membership via
  `BaseApiCtrl::authenticateRequest()` before allowing operations
- **Request Authorization**: Checks preventing users from editing/deleting tasks if they've been removed from the
  project after page load
- **Error Responses**: JSON error messages with appropriate HTTP status codes (403, 400, 500)

[//]: # (## Legal & Accessibility Compliance)

[//]: # ()

[//]: # (### WCAG 2.2 Compliance &#40;Level AA&#41;)

[//]: # ([//]: # &#40;TODO: Verify `### WCAG ??? Compliance` before delivering project, and switch to WCAG 2.2 Compliance&#41;)

[//]: # (- **Semantic HTML**: Proper use of headings, articles, sections, aria-labels,)

[//]: # (  alt-text &#40;[settings page]&#40;/app/Views/Pages/User/settings.php&#41;&#41;)

[//]: # (- **Color Contrast**: Text colors meet minimum 4.5:1 contrast ratio &#40;See list below&#41;)

[//]: # (- **Keyboard Navigation**: All interactive elements &#40;forms, buttons, modals&#41; accessible via keyboard)

[//]: # (- **Focus Indicators**: Visible focus states on all interactive elements &#40;Tailwind `focus:`&#41;)

[//]: # (- **Error Identification**: Clear error messages via toast notifications &#40;`$_SESSION['flash_errors']`&#41; describing what)

[//]: # (  went wrong)

[//]: # (- **Responsive Design**: Tablet- and Mobile-friendly layout adapting to different screen sizes)

[//]: # ()

[//]: # (<details>)

[//]: # (<summary><b>WCAG Color Contrast Examples</b></summary>)

[//]: # ()

[//]: # (![WCAG Contrast Example 1]&#40;docs/wcag/wcag1.png&#41;)

[//]: # (![WCAG Contrast Example 2]&#40;docs/wcag/wcag2.png&#41;)

[//]: # (![WCAG Contrast Example 3]&#40;docs/wcag/wcag3.png&#41;)

[//]: # (![WCAG Contrast Example 4]&#40;docs/wcag/wcag4.png&#41;)

[//]: # ()

[//]: # (</details>)

[//]: # (TODO: Rework `### GDPR Compliance` completely before delivering project, and verify it)

[//]: # (### GDPR Compliance)

[//]: # (- **Right of Access**: Users can view their account data &#40;username, email&#41; on)

[//]: # (  the [settings page]&#40;/app/Views/Pages/User/settings.php&#41;)

[//]: # (- **Right to Rectification**: Users can [edit]&#40;/app/Serv/UserServ.php&#41; and correct their username and email)

[//]: # (- **Right to Erasure**: Users can [delete]&#40;/app/Serv/UserServ.php&#41; their account with name confirmation)

[//]: # (- **Data Security**: Passwords hashed with bcrypt, secure session)

[//]: # (  management,[CSRF]&#40;/app/Core/Csrf.php&#41; & [CSP]&#40;/app/Core/Csp.php&#41; protection)

[//]: # (- **Data Minimization**: Only essential data collected &#40;username, email, password hash&#41; - no tracking or third-party)

[//]: # (  data sharing)
