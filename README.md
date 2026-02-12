# DropSaaS - Premium AI-Powered Laravel SaaS Platform

![Laravel 11](https://img.shields.io/badge/Laravel-11-FF2D20?style=for-the-badge&logo=laravel)
![PHP 8.2+](https://img.shields.io/badge/PHP-8.2%2B-777BB4?style=for-the-badge&logo=php)
![Style-Elite](https://img.shields.io/badge/Design-Glassmorphism-purple?style=for-the-badge)

DropSaaS is an elite, high-performance SaaS foundation built with Laravel 11. It features a stunning premium Glassmorphism UI, comprehensive subscription management, and native AI integration, designed for Envato Elite standards.

## 🚀 Key Features

- **Elite UI/UX**: Premium Glassmorphism design system using vanilla CSS and HSL variables.
- **AI Integration**: Native `AIService` for seamless integration with AI models (OpenAI, Gemini, etc.).
- **Subscription Engine**: Powered by **Laravel Cashier** with initial **Razorpay** support.
- **Multi-Language Support**: Advanced `LanguageService` for localization with RTL support.
- **Security & Authorization**: Strict typing, FormRequests, and Laravel Policies.
- **Admin Tools**: User impersonation, global settings management, and content control.
- **Performance**: Integrated caching strategies and optimized database schema.

## 🛠️ Technology Stack

- **Backend**: Laravel 11 (PHP 8.2+)
- **Database**: MySQL / PostgreSQL
- **Frontend**: Blade Templating, Vanilla CSS (Glassmorphism), Vite, Axios
- **Payments**: Laravel Cashier, Razorpay
- **Styling**: DS-Style Premium Framework (Custom Utility-First CSS)

## 📦 Production Installation

1. **Clone the repository**:
   ```bash
   git clone [repository-url]
   cd dropsaas
   ```

2. **Install Production Dependencies**:
   ```bash
   composer install --no-dev --optimize-autoloader
   npm install
   npm run build
   ```

3. **Environment Setup**:
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Database Configuration**:
   Update your `.env` with production DB credentials and run:
   ```bash
   php artisan migrate --force
   ```

5. **Security Checklist**:
   - Set `APP_DEBUG=false`
   - Set `APP_ENV=production`
   - Run `php artisan config:cache`, `php artisan route:cache`, `php artisan view:cache`

## 🏗️ Architecture

- **Skinny Controllers**: All business logic resides in `app/Services`.
- **Validation**: Strict use of `FormRequests`.
- **Localisation**: No hardcoded text. All user-facing strings are in `lang/`.
- **DRY Principle**: Shared logic extracted into Traits and Helpers.

## 📄 License

This project is proprietary software. All rights reserved.

---
Built with ❤️ by **Envato Elite Laravel Architect**.
