# Shop App

A functionally complete e-commerce web application built with Symfony 7,
developed as a learning project — with particular attention to testing,
security, and understanding each part of the framework.
The focus is on back-end logic; styling is intentionally left for a later stage.


## Features

- **Catalogue** — products and categories, with secure image uploads
- **Accounts** — registration, login/logout, hashed passwords
- **Cart** — session-based cart with a custom service, live counter
- **Checkout** — order creation with prices frozen at purchase time
- **Payment** — Stripe Checkout with a signature-verified, idempotent webhook
- **Emails** — asynchronous order confirmations via Messenger
- **Back-office** — EasyAdmin panel, restricted to administrators
- **Fine-grained access** — a Voter ensures users only see their own orders
- **Order status workflow** — controlled transitions (pending → paid → …)
- **Interactivity** — live cart counter and instant product search (Symfony UX)
- **Customer area** — order history

## Tech stack

- Symfony 7 (webapp skeleton)
- PostgreSQL
- Doctrine ORM
- PHPUnit, Foundry + DAMA for fixtures and tests
- Stripe (payment), Symfony Messenger (async), VichUploader (image uploads)
- Symfony UX (Turbo, Live Components)
- GitHub Actions (continuous integration)

## Requirements

- PHP 8.4
- PostgreSQL
- Composer
- Symfony CLI (recommended)

## Getting started

```bash
# Install dependencies
composer install

# Configure your environment
# Copy .env and set your own values in .env.local
# (DATABASE_URL, STRIPE_SECRET_KEY, STRIPE_WEBHOOK_SECRET, MAILER_DSN…)

# Create the database and run migrations
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate

# Load sample data (optional)
php bin/console doctrine:fixtures:load

# Start the server
symfony serve
```

## Testing

```bash
php bin/phpunit
```

Tests run automatically on every push via GitHub Actions.

## Notes

- Prices are stored in cents (integers) to avoid rounding issues.
- The Messenger worker must run to send emails: `php bin/console messenger:consume async`
- For local Stripe webhook testing, use the Stripe CLI to forward events.
