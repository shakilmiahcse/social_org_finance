# SocialOrg Finance - Social Organization Finance & Donation Management System

SocialOrg Finance is a comprehensive, modern finance and donation tracking web application designed specifically for social organizations, NGOs, and non-profits. Built with a robust Laravel backend and a reactive Vue.js 3 frontend via Inertia.js, it offers smooth user experiences with real-time feedback, detailed reporting, and secure role-based access control.

---

## 🚀 Features

### 📊 Interactive Dashboard
- **Financial Summary**: Real-time balance tracker, total incoming (credits), total outgoing (debits), and monthly progress charts.
- **Allocation & Trends**: Visual representation of fund allocations and monthly transaction trends using Chart.js.
- **Quick Insights**: Top donors list, low-balance alerts, and direct shortcut controls for adding incomes or expenses.

### 👥 Donor & Donation Management
- **Donor Database**: Complete registry of organization donors with search and filtering capabilities.
- **Donation History**: Track individual contributions and detailed transaction histories for each donor.
- **Dynamic Selectors**: Auto-suggest and search-enabled donor dropdown fields for quick receipting.

### 💼 Fund & Campaign Tracking
- **Multi-Fund Tracking**: Segment finance records into separate, custom funds (e.g., General Fund, Education, Disaster Relief).
- **Campaign Adjustments**: Manage allocations and adjust balances across different funds and active campaigns.
- **History Logs**: Visual chronological trace of inflows and outflows for specific funds.

### 💸 Transaction Ledger (Income & Expense)
- **Credit & Debit Logging**: Standard ledger entries specifying amount, category, donor, fund, and payment status.
- **Dynamic Receipts**: Generate and export professionally formatted printable invoices/receipts directly in the browser.

### 📋 Role-Based Access Control (RBAC)
- **Role Customization**: Define custom roles (e.g., Admin, Accountant, Moderator) with tailored system permissions.
- **Permission Matrix**: Control page-level access (view, create, edit, delete transactions/donors/funds).

### 🔍 Advanced Reports & Auditing
- **Flexible Filters**: Filter transactions by date range, donor, fund, category, and type.
- **Data Exporting**: Seamlessly download filtered reports in **Excel (.xlsx)** or **PDF** format.
- **System Activity Log**: Full audit trail of admin actions (creation, updates, deletions) showing who did what and when.

### ⚙️ Organization & Custom Settings
- **Org Customization**: Set up organization name, contact info, logo, and metadata.
- **Receipt Settings**: Configure signature lines, headers, footers, and receipt-specific text.
- **Appearance**: Built-in support for theme settings (light/dark mode toggle).

---

## 🛠️ Tech Stack

### Backend
- **Framework**: Laravel 12.x (PHP 8.2+)
- **Session & Caching**: Database-driven sessions and cache stores
- **Packages**:
  - `spatie/laravel-permission` (RBAC)
  - `spatie/laravel-activitylog` (Audit Trail)
  - `tightenco/ziggy` (Routing bridge to Vue)

### Frontend
- **Framework**: Vue 3 (Composition API / `<script setup>` with TypeScript)
- **State & Routing**: Inertia.js (v2.0.0-beta.3)
- **Build Tool**: Vite
- **Styling**: Tailwind CSS, CSS Variables for system styling, Tailwind Animate
- **Key Libraries**:
  - `chart.js` & `vue-chartjs` (Data visualizations)
  - `lucide-vue-next` (Modern icons)
  - `exceljs` & `file-saver` (Excel reports generation)
  - `jspdf` & `jspdf-autotable` (PDF report & receipt building)
  - `sweetalert2` (Custom modals and alerts)
  - `@vuepic/vue-datepicker` (Modern date selections)

---

## 💻 Installation & Setup

Follow these steps to run the project locally:

### Prerequisites
- PHP >= 8.2
- Composer
- Node.js & npm
- SQLite / MySQL

### 1. Clone the Repository
```bash
git clone https://github.com/shakilmiahcse/social_org_finance.git
cd social_org_finance
```

### 2. Install PHP Dependencies
```bash
composer install
```

### 3. Install NPM Packages
```bash
npm install
```

### 4. Environment Configuration
Copy the template `.env.example` file and configure it:
```bash
cp .env.example .env
```
Generate the Laravel application key:
```bash
php artisan key:generate
```

### 5. Setup Database
If using SQLite (default):
Create the SQLite database file:
- On Linux/macOS: `touch database/database.sqlite`
- On Windows: `New-Item database/database.sqlite` (PowerShell) or manually create it.

If using MySQL, configure your DB details in `.env`:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=social_org_finance
DB_USERNAME=root
DB_PASSWORD=yourpassword
```

Run migrations and seed the database with initial/dummy data:
```bash
php artisan migrate --seed
```

### 6. Start the Development Servers
You can run Laravel's development server, Vite, and the queue worker simultaneously using the custom composer shortcut:
```bash
composer run dev
```
Alternatively, run them separately in different terminal tabs:
```bash
# Start Laravel server
php artisan serve

# Start Vite hot reloading
npm run dev

# Start Queue worker
php artisan queue:listen
```

Access the application at `http://127.0.0.1:8000` (or the URL output by `php artisan serve`).

---

## 🔒 License
This project is licensed under the MIT License.
