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