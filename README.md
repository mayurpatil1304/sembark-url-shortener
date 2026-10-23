<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework.

You may also try the [Laravel Bootcamp](https://bootcamp.laravel.com), where you will be guided through building a modern Laravel application from scratch.

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the [Laravel Partners program](https://partners.laravel.com).

### Premium Partners

- **[Vehikl](https://vehikl.com/)**
- **[Tighten Co.](https://tighten.co)**
- **[WebReinvent](https://webreinvent.com/)**
- **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
- **[64 Robots](https://64robots.com)**
- **[Curotec](https://www.curotec.com/services/technologies/laravel/)**
- **[Cyber-Duck](https://cyber-duck.co.uk)**
- **[DevSquad](https://devsquad.com/hire-laravel-developers)**
- **[Jump24](https://jump24.co.uk)**
- **[Redberry](https://redberry.international/laravel/)**
- **[Active Logic](https://activelogic.com)**
- **[byte5](https://byte5.de)**
- **[OP.GG](https://op.gg)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).




//

# Sembark URL Shortener

A company-based URL shortening application built with Laravel, PHP, and MySQL. The application supports role-based access, short URL creation, click tracking, and invitation-based account registration.

## Features

- **Role-based access:** SuperAdmin, Admin, Member, Sales, and Manager roles.
- **Company management:** Organize users and URLs by company.
- **Invitation-based registration:** Invite users by email using secure, time-limited invitation tokens.
- **Short URL creation:** Generate a short code for a valid destination URL.
- **Redirect and click tracking:** Redirect visitors to the original URL and record clicks.
- **Role-scoped dashboard:** Display company, team, and URL information according to the signed-in user's permissions.
- **Input validation:** Validate submitted URLs and invitation details.
- **Database-backed storage:** Store users, companies, invitations, and short URLs in MySQL.

## Technology Stack

- PHP 8.3
- Laravel 12
- MySQL 8
- Blade templates
- Laravel authentication
- Bootstrap 5 (CDN)
- Composer
- Node.js and npm, if required by the project's frontend assets

> Confirm the Laravel version and dependencies in `composer.json` before submitting.

## Requirements

Install the following before setting up the project:

- PHP version supported by the project's Laravel version
- Composer
- MySQL 8 or a compatible MySQL server
- Git
- Node.js and npm if frontend assets need to be built

Enable the PHP extensions required by Laravel and the dependencies listed in `composer.json`.

## Installation

### 1. Clone the repository

```bash
git clone <YOUR_GITHUB_REPOSITORY_URL>
cd <YOUR_PROJECT_DIRECTORY>
```

### 2. Install PHP dependencies

```bash
composer install
```

### 3. Configure the environment

Create your local environment file:

```bash
cp .env.example .env
```

On Windows Command Prompt, you can use:

```bat
copy .env.example .env
```

Update `.env` with your local database connection and other required settings. Never commit your real `.env` file.

Example database configuration:

```dotenv
APP_NAME="Sembark URL Shortener"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://127.0.0.1:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=sembark_url_shortener
DB_USERNAME=your_local_database_user
DB_PASSWORD=your_local_database_password

MAIL_MAILER=log
MAIL_FROM_ADDRESS="noreply@sembark.local"
MAIL_FROM_NAME="${APP_NAME}"
```

Create the database in MySQL before running migrations.

### 4. Generate the application key

```bash
php artisan key:generate
```

### 5. Run migrations and seeders

```bash
php artisan migrate --seed
```

The seeders may create a SuperAdmin account and demo role accounts, depending on the configured seeders.

Configure the required local environment variables before seeding. Use strong, private passwords and never publish them in the README or repository.

### 6. Install frontend dependencies

If the project includes a `package.json`, run:

```bash
npm install
npm run build
```

For local frontend development, use:

```bash
npm run dev
```

### 7. Start the application

```bash
php artisan serve
```

Open:

```text
http://127.0.0.1:8000
```

## Demo Accounts

If `DemoUsersSeeder` is enabled, it creates accounts for the following roles:

| Role | Demo email |
|---|---|
| SuperAdmin | `superadmin@sembark.local` |
| Admin | `admin@sembark.local` |
| Member | `member@sembark.local` |
| Sales | `sales@sembark.local` |
| Manager | `manager@sembark.local` |

The SuperAdmin password is configured using `SUPER_ADMIN_PASSWORD`. The other demo accounts use `DEMO_USERS_PASSWORD` if that is how the seeder is configured.

Set these variables locally before running the relevant seeder. These are development accounts only; do not use them in production.

## Invitation Workflow

1. An authorized administrator sends an invitation.
2. The application creates a token and stores its hash with an expiry time.
3. The invitation link is sent using the configured mail driver.
4. The invited user opens the link and submits the registration form.
5. The application validates the invitation and creates the account with the invitation's company and role.

With `MAIL_MAILER=log`, invitation messages are written to the Laravel log rather than delivered to a real inbox. Check `storage/logs/laravel.log` during local testing.

## Short URL Workflow

1. An authorized user submits a destination URL.
2. The application validates the URL and generates a unique short code.
3. The short URL is saved with its company and creator.
4. A visitor opens the short URL.
5. The application increments the click count and redirects the visitor to the saved destination.

## Testing

Run the automated test suite with:

```bash
php artisan test
```

If tests use `RefreshDatabase`, configure a dedicated test database so test runs do not affect development data.

Manually verify:

- Each role can access only its permitted pages and actions.
- Users cannot access another company's private data.
- Invitation tokens cannot be reused or accepted after expiry.
- Newly invited users are assigned to the invitation's company.
- Short URL creation validates the destination.
- Redirects reach the original destination and increment the click count.
- Unknown short codes return a 404 response.

## Security Notes

- Keep `.env` and all real credentials out of version control.
- Do not publish production passwords, API keys, mail credentials, or database credentials.
- Enforce authorization on the server; hiding a button is not sufficient.
- Use HTTPS and production-appropriate mail settings when deploying.
- Review invitation expiry, token handling, validation, and role permissions before deployment.

## AI Assistance

See [AI_ASSISTANCE.md](AI_ASSISTANCE.md) for a description of AI-assisted development and the verification responsibilities for this project.

## License

Add the license required by your assignment or organization. Do not claim an open-source license unless you intend to publish the project under its terms.

