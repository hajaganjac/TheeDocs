# AI Webshop

A PHP + SQLite3 demo storefront that showcases AI-themed products. It ships with a Bootstrap 5 UI, a simulated checkout experience, and an admin area for managing catalog items and fulfillment.

## Features

- **Product catalog** rendered with Bootstrap 5 cards.
- **Cart & simulated checkout** that stores orders in SQLite and confirms via email.
- **Email notifications** using PHP's native `mail()` for customers and the shop admin.
- **Admin dashboard** with password protection, order management, shipping status, and downloadable PDF labels.
- **Product uploader** allowing admins to add items and upload product photos.

## Requirements

- PHP 8.1+ with the SQLite3 extension enabled.
- A web server capable of running PHP (Apache, Nginx with PHP-FPM, the built-in PHP server, etc.).
- Mail transport configured so that `mail()` can deliver email (e.g., sendmail, postfix, MailHog during development).

## Getting Started

1. Install dependencies (only core PHP extensions are required).
2. Run the database setup script once:

   ```bash
   php setup.php
   ```

   This creates `data/shop.sqlite` with the required tables.

3. Start the PHP built-in server (or configure your web server) pointing at the `public/` directory:

   ```bash
   php -S localhost:8000 -t public
   ```

4. Visit `http://localhost:8000/index.php` to browse products.

5. Access the admin dashboard at `http://localhost:8000/admin/login.php` using the password in `config.php` (default: `changeme`). Update this password before deploying anywhere public.

## Configuration

Update `config.php` to match your environment:

- `db_path`: SQLite database location.
- `upload_dir`: Folder where product images are stored.
- `admin_email` & `from_email`: Addresses used for order notifications.
- `admin_password`: Plain-text password for the admin portal.
- `store_name`: Display name for storefront and notifications.

## Email Delivery

The checkout flow sends two emails via `mail()`:

1. A confirmation to the customer containing the order summary.
2. A notification to the configured admin email.

For local development, consider routing mail to a testing tool (e.g., MailHog). In production, ensure your PHP configuration can deliver email.

## File Uploads

Product images are stored in the `uploads/` directory (git-ignored). Ensure the PHP process has write permissions to this folder.

## PDF Shipping Labels

Admins can download a PDF shipping label from the Orders page. The PDF is generated on the fly with a lightweight custom renderer and includes order metadata and line items for quick fulfillment.

## Notes

- This demo is intentionally lightweight and omits advanced hardening. Add validation, CSRF protection, and HTTPS before putting it on the open internet.
- SQLite is perfect for demos and small catalogs. For larger deployments, swap in MySQL/PostgreSQL and update the database helper accordingly.
