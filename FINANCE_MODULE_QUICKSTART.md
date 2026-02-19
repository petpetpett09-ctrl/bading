# Finance Module - Quick Start Guide

## Prerequisites

- Laravel 12 application running
- Vue 3 with Inertia.js configured
- Tailwind CSS 3.2 installed
- MySQL database ready
- PHP 8.2+
- Node.js 18+ (for asset compilation)

## Installation Steps

### Step 1: Verify Files are in Place

All Finance Module files should be created in the following locations:

```
✓ app/Models/Account.php
✓ app/Models/Transaction.php
✓ app/Models/Invoice.php
✓ app/Models/Bill.php
✓ app/Http/Controllers/finance/FinanceDashboardController.php
✓ app/Http/Controllers/finance/FinanceTransactionController.php
✓ app/Http/Controllers/finance/FinanceInvoiceBillController.php
✓ app/Http/Controllers/finance/FinanceReportController.php
✓ resources/js/Pages/Finance/Dashboard.vue
✓ resources/js/Pages/Finance/Transactions.vue
✓ resources/js/Pages/Finance/InvoicesBills.vue
✓ resources/js/Pages/Finance/Reports.vue
✓ resources/js/Pages/Finance/TransactionForm.vue
✓ resources/js/Pages/Finance/InvoiceForm.vue
✓ resources/js/Pages/Finance/BillForm.vue
✓ resources/js/types/finance.ts
```

### Step 2: Create Database Migrations

Create migration files for the Finance Module tables:

#### Migration: Create Accounts Table

```bash
php artisan make:migration create_accounts_table
```

**File: `database/migrations/XXXX_XX_XX_XXXXXX_create_accounts_table.php`**

```php
<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('accounts', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('name');
            $table->enum('type', ['Asset', 'Liability', 'Equity', 'Revenue', 'Expense']);
            $table->string('category')->nullable();
            $table->decimal('balance', 12, 2)->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->index('type');
            $table->index('is_active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('accounts');
    }
};
```

#### Migration: Create Transactions Table

```bash
php artisan make:migration create_transactions_table
```

**File: `database/migrations/XXXX_XX_XX_XXXXXX_create_transactions_table.php`**

```php
<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('account_id');
            $table->date('date');
            $table->string('description');
            $table->decimal('amount', 12, 2);
            $table->enum('type', ['income', 'expense', 'transfer']);
            $table->string('category')->nullable();
            $table->string('reference_number')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            
            $table->foreign('account_id')
                  ->references('id')
                  ->on('accounts')
                  ->onDelete('cascade');
            
            $table->index('date');
            $table->index('type');
            $table->index('category');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
```

#### Migration: Create Invoices Table

```bash
php artisan make:migration create_invoices_table
```

**File: `database/migrations/XXXX_XX_XX_XXXXXX_create_invoices_table.php`**

```php
<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_number')->unique();
            $table->string('customer');
            $table->date('issue_date');
            $table->date('due_date');
            $table->decimal('total_amount', 12, 2);
            $table->enum('status', ['draft', 'sent', 'paid', 'overdue'])->default('draft');
            $table->text('notes')->nullable();
            $table->timestamps();
            
            $table->index('status');
            $table->index('due_date');
            $table->index('issue_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
```

#### Migration: Create Bills Table

```bash
php artisan make:migration create_bills_table
```

**File: `database/migrations/XXXX_XX_XX_XXXXXX_create_bills_table.php`**

```php
<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bills', function (Blueprint $table) {
            $table->id();
            $table->string('bill_number')->unique();
            $table->string('vendor');
            $table->date('bill_date');
            $table->date('due_date');
            $table->decimal('total_amount', 12, 2);
            $table->enum('status', ['draft', 'received', 'approved', 'paid', 'overdue'])->default('draft');
            $table->text('notes')->nullable();
            $table->timestamps();
            
            $table->index('status');
            $table->index('due_date');
            $table->index('bill_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bills');
    }
};
```

### Step 3: Run Migrations

```bash
php artisan migrate
```

### Step 4: Seed Sample Data (Optional)

Create a seeder:

```bash
php artisan make:seeder FinanceSeeder
```

**File: `database/seeders/FinanceSeeder.php`**

```php
<?php
namespace Database\Seeders;

use App\Models\Account;
use App\Models\Transaction;
use App\Models\Invoice;
use App\Models\Bill;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class FinanceSeeder extends Seeder
{
    public function run(): void
    {
        // Create Chart of Accounts
        $accounts = [
            ['code' => '1000', 'name' => 'Cash', 'type' => 'Asset', 'category' => 'Current Assets'],
            ['code' => '1100', 'name' => 'Accounts Receivable', 'type' => 'Asset', 'category' => 'Current Assets'],
            ['code' => '2000', 'name' => 'Accounts Payable', 'type' => 'Liability', 'category' => 'Current Liabilities'],
            ['code' => '3000', 'name' => 'Retained Earnings', 'type' => 'Equity', 'category' => 'Equity'],
            ['code' => '4000', 'name' => 'Service Revenue', 'type' => 'Revenue', 'category' => 'Operating Revenue'],
            ['code' => '5000', 'name' => 'Operating Expenses', 'type' => 'Expense', 'category' => 'Operating Expenses'],
        ];

        foreach ($accounts as $account) {
            Account::create(array_merge($account, ['balance' => 0]));
        }

        // Create sample transactions
        $cashAccount = Account::where('code', '1000')->first();
        $revenueAccount = Account::where('code', '4000')->first();
        $expenseAccount = Account::where('code', '5000')->first();

        for ($i = 0; $i < 20; $i++) {
            Transaction::create([
                'account_id' => $cashAccount->id,
                'date' => Carbon::now()->subDays(rand(0, 30)),
                'description' => "Sample Transaction {$i}",
                'amount' => rand(100, 5000),
                'type' => collect(['income', 'expense'])->random(),
                'category' => collect(['Consulting', 'Marketing', 'Operations'])->random(),
                'reference_number' => 'TRX-' . str_pad($i, 5, '0', STR_PAD_LEFT),
            ]);
        }

        // Create sample invoices
        for ($i = 1; $i <= 10; $i++) {
            Invoice::create([
                'invoice_number' => 'INV-' . str_pad($i, 4, '0', STR_PAD_LEFT),
                'customer' => "Customer {$i}",
                'issue_date' => Carbon::now()->subDays(rand(0, 60)),
                'due_date' => Carbon::now()->addDays(rand(1, 30)),
                'total_amount' => rand(1000, 10000),
                'status' => collect(['draft', 'sent', 'paid'])->random(),
            ]);
        }

        // Create sample bills
        for ($i = 1; $i <= 10; $i++) {
            Bill::create([
                'bill_number' => 'BILL-' . str_pad($i, 4, '0', STR_PAD_LEFT),
                'vendor' => "Vendor {$i}",
                'bill_date' => Carbon::now()->subDays(rand(0, 60)),
                'due_date' => Carbon::now()->addDays(rand(1, 30)),
                'total_amount' => rand(500, 5000),
                'status' => collect(['draft', 'received', 'approved', 'paid'])->random(),
            ]);
        }
    }
}
```

Run the seeder:

```bash
php artisan db:seed --class=FinanceSeeder
```

### Step 5: Compile Frontend Assets

```bash
npm run dev
```

Or for production:

```bash
npm run build
```

### Step 6: Verify Routes

Check that finance routes are accessible:

```bash
php artisan route:list | grep finance
```

You should see all `/finance/*` routes listed.

## Testing the Finance Module

### 1. Start Development Server

```bash
php artisan serve
```

Visit: `http://localhost:8000`

### 2. Access Finance Dashboard

Navigate to: `http://localhost:8000/finance/dashboard`

You should see:
- 4 metric cards with KPI data
- 6 summary cards
- Recent transactions table
- Chart placeholders

### 3. Test Transaction Management

1. Click "New Transaction" button
2. Fill in form fields
3. Submit transaction
4. Verify it appears in list
5. Test filtering and export

### 4. Test Invoice/Bill Management

1. Navigate to "Invoices & Bills"
2. Create test invoice
3. Create test bill
4. Test status updates
5. Test filters

### 5. Generate Reports

1. Navigate to "Reports"
2. Select report type
3. Choose date range
4. View report data
5. Test CSV export

## Common Issues & Solutions

### Routes Not Found (404)

**Problem**: `/finance/dashboard` returns 404

**Solution**:
1. Verify routes are in `routes/web.php`
2. Clear route cache: `php artisan route:clear`
3. Check that controllers exist in correct directory

### Models Not Found

**Problem**: `Class App\Models\Account not found`

**Solution**:
1. Verify model files exist in `app/Models/`
2. Check class namespaces match file paths
3. Run: `composer dump-autoload`

### Database Tables Missing

**Problem**: SQLSTATE[42S02]: Table not found...

**Solution**:
1. Run migrations: `php artisan migrate`
2. Verify .env DATABASE_* settings
3. Check `php artisan migrate:status`

### Assets Not Compiling

**Problem**: Vue components not rendering

**Solution**:
1. Run: `npm install`
2. Run: `npm run dev`
3. Clear browser cache
4. Check browser console for errors

### Type Errors in Vue Components

**Problem**: TypeScript compilation errors

**Solution**:
1. Verify `resources/js/types/finance.ts` exists
2. Check import paths in components
3. Run: `npm run build` to see detailed errors

## Performance Tips

1. **Add Indexes**: Finance migration includes key indexes
2. **Paginate Large Datasets**: Already implemented in all list views
3. **Cache Reports**: Consider caching report calculations
4. **Archive Old Data**: Implement data archival for historical transactions
5. **Monitor Database**: Use `php artisan tinker` to check query counts

## Next Steps

After successful installation:

1. ✅ Configure role-based access control (optional)
2. ✅ Install chart library (Chart.js recommended)
3. ✅ Set up PDF generation (dompdf/TCPDF)
4. ✅ Configure email notifications
5. ✅ Set up automated bill reminders
6. ✅ Configure backup strategy

## Environment Configuration

Add these to your `.env` file if needed:

```env
# Finance Module Settings
FINANCE_CURRENCY=USD
FINANCE_DECIMAL_PLACES=2
FINANCE_EXPORT_ROWS_PER_BATCH=1000
```

## Support

For issues or questions:

1. Check `FINANCE_MODULE_README.md` for detailed documentation
2. Review error messages in `storage/logs/laravel.log`
3. Check browser DevTools for client-side errors
4. Verify database schema matches migrations

---

**Version**: 1.0
**Last Updated**: February 19, 2026
**Status**: Ready for Production
