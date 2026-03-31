# 🧵 TMS — Textile Management System

A modern, full-stack **Textile Management System** built with **Laravel 11**, **Vue 3**, and **Inertia.js**. Manage your textile business operations end-to-end: from master data setup to purchase and sales transactions.

---

## ✨ Features

### 🔐 Authentication
- User registration & login powered by **Laravel Breeze**
- Profile management (update name, email, password)

### 📦 Master Data

#### Categories
- Create, edit, delete product categories
- Organize products by textile type (e.g., Cotton, Polyester, Batik)

#### Suppliers
- Manage supplier directory with name, phone, and address
- Link suppliers to purchase orders

#### Customers
- Manage customer directory with name, phone, and address
- Link customers to sales orders

#### Products
- Create products with `name`, `code`, `description`, `price`, and `stock`
- Assign each product to a category

### 🛒 Transactions

#### Purchase Orders
- Record fabric/material purchases from suppliers
- Add multiple line items (product + price + date) per purchase
- View purchase history with supplier info and totals

#### Sales Orders
- Record sales to customers
- Add multiple line items per sale
- View sales history with customer info and totals

---

## 🛠 Tech Stack

| Layer | Technology |
|---|---|
| Backend | Laravel 11 (PHP 8.2+) |
| Frontend | Vue 3 + Inertia.js |
| Auth | Laravel Breeze |
| Styling | Tailwind CSS |
| Build Tool | Vite |
| Routing (SPA) | Ziggy (Laravel routes in JS) |
| Database | MySQL |

---

## 🚀 Getting Started

### Prerequisites

- PHP 8.2+
- Composer
- Node.js 18+
- MySQL

### 1. Clone the repository

```bash
git clone https://github.com/your-username/tms2.git
cd tms2
```

### 2. Install PHP dependencies

```bash
composer install
```

### 3. Install Node dependencies

```bash
npm install
```

### 4. Configure environment

```bash
cp .env.example .env
php artisan key:generate
```

Then edit `.env` and set your database credentials:

```env
APP_URL=http://localhost

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=tms
DB_USERNAME=root
DB_PASSWORD=

VITE_APP_URL=http://localhost:5173  # Vite dev server origin
```

### 5. Run database migrations

```bash
php artisan migrate
```

### 6. (Optional) Seed sample data

```bash
php artisan db:seed
```

### 7. Start the development servers

Open two terminals and run each command in its own terminal:

```bash
# Terminal 1 — Laravel backend
php artisan serve

# Terminal 2 — Vite frontend (hot reload)
npm run build
```

Then open your browser at: **http://localhost:8000** *(or your configured `APP_URL`)*

---

## 🔑 Default Credentials

After seeding (if a seeder is configured):

| Field | Value |
|---|---|
| Email | admin@tms.com |
| Password | password |

---