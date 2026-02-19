# Finance Module - Developer Quick Reference

## Quick Links to Key Files

### Models
- **Account.php** - `app/Models/Account.php`
  - Scopes: `active()`, `byType()`, `byCategory()`
  
- **Transaction.php** - `app/Models/Transaction.php`
  - Scopes: `byType()`, `byDateRange()`, `byCategory()`, `byAccount()`, `search()`, `latest()`
  
- **Invoice.php** - `app/Models/Invoice.php`
  - Scopes: `unpaid()`, `overdue()`, `byDateRange()`, `latest()`
  
- **Bill.php** - `app/Models/Bill.php`
  - Scopes: `unpaid()`, `overdue()`, `byDateRange()`, `latest()`

### Controllers
- **FinanceDashboardController.php** - `app/Http/Controllers/finance/`
  - Methods: `index()`, `getCashOnHand()`, `calculateProfitMetrics()`, `getMonthlyTrend()`
  
- **FinanceTransactionController.php** - CRUD + Export
  - Methods: `index()`, `create()`, `store()`, `edit()`, `update()`, `destroy()`, `export()`
  
- **FinanceInvoiceBillController.php** - Invoice & Bill Management
  - Methods: `index()` (both), `createInvoice()`, `storeInvoice()`, `createBill()`, `storeBill()`
  - Status Update: `markInvoicePaid()`, `markBillPaid()`
  - Download: `downloadInvoice()`, `downloadBill()`
  
- **FinanceReportController.php** - 5 Report Types
  - Methods: `index()`, `generateReport()`, `exportReport()`
  - Generators: `generateIncomeStatement()`, `generateCashFlow()`, `generateExpenseByCategory()`

### Vue Components

#### Main Pages
- **Dashboard.vue** - Route: `/finance/dashboard`
  - Props: `metrics` (DashboardMetrics), `recent_transactions` (Transaction[])
  
- **Transactions.vue** - Route: `/finance/transactions`
  - Props: `transactions` (paginated), `categories`, `accounts`, `filters`
  
- **InvoicesBills.vue** - Route: `/finance/invoices-bills`
  - Props: `invoices` (Invoice[]), `bills` (Bill[]), `filters`
  
- **Reports.vue** - Route: `/finance/reports`
  - Props: `report` (ReportData), `report_type` (string), `date_range`

#### Form Pages
- **TransactionForm.vue** - Route: `/finance/transactions/create`, `/finance/transactions/{id}/edit`
- **InvoiceForm.vue** - Route: `/finance/invoices/create`
- **BillForm.vue** - Route: `/finance/bills/create`

## Common Code Snippets

### Add Transaction Filter in Controller
```php
protected function applyFilters($query, array $filters)
{
    if (!empty($filters['start_date'])) {
        $query->where('date', '>=', $filters['start_date']);
    }
    if (!empty($filters['end_date'])) {
        $query->where('date', '<=', $filters['end_date']);
    }
    if (!empty($filters['type'])) {
        $query->where('type', $filters['type']);
    }
    return $query;
}
```

### Get Dashboard Metrics in Controller
```php
$metrics = [
    'revenue' => Transaction::where('type', 'income')
        ->whereMonth('date', now()->month)
        ->sum('amount'),
    'expenses' => Transaction::where('type', 'expense')
        ->whereMonth('date', now()->month)
        ->sum('amount'),
];
```

### Use Form in Vue Component
```typescript
const form = useForm({
    date: '',
    description: '',
    amount: 0,
    type: 'income',
    category: '',
});

const submit = () => {
    form.post(route('finance.transactions.store'), {
        onSuccess: () => {
            message.value = 'Transaction created successfully';
        },
    });
};
```

### Format Currency in Vue
```typescript
const formatCurrency = (amount: number) => {
    return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: 'USD',
    }).format(amount);
};
```

### Format Date in Vue
```typescript
const formatDate = (date: string) => {
    return new Date(date).toLocaleDateString('en-US', {
        month: 'short',
        day: 'numeric',
        year: 'numeric',
    });
};
```

## Database Query Examples

### Get Monthly Revenue Trend (12 months)
```php
$monthlyRevenue = [];
for ($i = 11; $i >= 0; $i--) {
    $date = now()->subMonths($i);
    $monthlyRevenue[] = Transaction::where('type', 'income')
        ->whereYear('date', $date->year)
        ->whereMonth('date', $date->month)
        ->sum('amount');
}
```

### Get Overdue Invoices
```php
$overdueInvoices = Invoice::where('status', '!=', 'paid')
    ->where('due_date', '<', now())
    ->get();
```

### Get Account Balance
```php
$balance = Transaction::where('account_id', $accountId)
    ->sum('amount');
```

## Route Examples

### Navigate to Dashboard
```typescript
// In Vue component
import { route } from '@inertiajs/vue3';

const goToDashboard = () => {
    window.location.href = route('finance.dashboard');
};
```

### Create Route URL
```typescript
const createTransactionUrl = route('finance.transactions.create');
const editTransactionUrl = route('finance.transactions.edit', transactionId);
const deleteTransactionUrl = route('finance.transactions.destroy', transactionId);
```

## TypeScript Type Examples

### Use Transaction Interface
```typescript
import { defineProps } from 'vue';
import type { Transaction } from '@/types/finance';

interface Props {
    transactions: Transaction[];
}

const props = defineProps<Props>();

// Now props.transactions is type-safe
props.transactions.forEach(tx => {
    console.log(tx.description); // TypeScript knows it exists
});
```

## Testing Workflows

### Test Dashboard Metrics
```bash
php artisan tinker
>>> use App\Models\Transaction;
>>> Transaction::where('type', 'income')->whereMonth('date', now()->month)->sum('amount');
# Should return current month revenue
```

### Test Transaction Creation
```bash
php artisan tinker
>>> use App\Models\Transaction;
>>> Transaction::create(['date' => now(), 'description' => 'Test', 'amount' => 100, 'type' => 'income', 'account_id' => 1]);
```

### Test Report Generation
```bash
# Visit http://localhost:8000/finance/reports
# Select "Income Statement"
# Should show revenue and expense summary
```

## Debugging Tips

### Check Query Logs
```php
// In controller
DB::enableQueryLog();
// ... your code ...
dd(DB::getQueryLog());
```

### Check Vue Props
```vue
<script setup lang="ts">
import { defineProps } from 'vue';

const props = defineProps<{ transaction: any }>();

// In template
{{ JSON.stringify(props.transaction, null, 2) }}
</script>
```

### Check Form Errors
```vue
<div v-if="form.errors.amount" class="text-red-500">
    {{ form.errors.amount }}
</div>
```

### Check Browser Console
```javascript
// In browser DevTools console
// Check Inertia component data:
window.Inertia.page.props.transactions
// Check form data:
// Inspect form object in Vue DevTools
```

## Common Tasks

### Add New Dashboard Metric
1. Update FinanceDashboardController.php `index()` method
2. Add metric calculation (e.g., new `getCashFlow()` method)
3. Update DashboardMetrics interface in finance.ts
4. Update Dashboard.vue to display metric card

### Add New Filter to Transactions
1. Update FinanceTransactionController.php `index()` method
2. Add filter logic in query builder
3. Update Transactions.vue filter section with new field
4. Update route query parameters

### Add New Report Type
1. Create generator method in FinanceReportController.php
2. Add to switch/match in `generateReport()` method
3. Create corresponding UI section in Reports.vue
4. Add interface for report data in finance.ts

### Export New Data Type
1. Add export logic in controller
2. Use CSV helper: `response()->streamDownload()`
3. Add button in Vue component to call export route

## Performance Optimization Tips

1. **Use Pagination**: Always paginate large datasets (implemented as 20 per page)
2. **Use Indexes**: Ensure indexes on frequently filtered columns
3. **Use Select**: Only select needed columns: `select('id', 'name', 'amount')`
4. **Use Caching**: Cache dashboard metrics if recalculated frequently
5. **Use Eager Loading**: `with('account')` to prevent N+1 queries

## Security Reminders

✅ Always validate input: `validate(['amount' => 'required|numeric|min:0.01'])`
✅ Always use middleware: `middleware(['auth'])`
✅ Always use CSRF tokens (Inertia handles automatically)
✅ Always use Eloquent (prevents SQL injection)
✅ Always escape output (Vue template handles automatically)

## File Naming Conventions

- **Models**: PascalCase, singular (Account.php, Transaction.php)
- **Controllers**: PascalCase, Controller suffix (FinanceController.php)
- **Views/Components**: PascalCase, .vue extension (Dashboard.vue)
- **Routes**: kebab-case URL, snake_case route names (finance.transactions.store)
- **Routes/Methods**: RESTful (index, create, store, edit, update, destroy)

## Useful Commands

```bash
# List all finance routes
php artisan route:list | grep finance

# Find references to model
php artisan tinker
>>> use App\Models\Transaction;
>>> Transaction::count();

# Check database schema
php artisan db:show

# Clear caches
php artisan cache:clear

# Run tests
php artisan test

# Generate migration
php artisan make:migration migration_name
```

## Documentation Files

- **FINANCE_MODULE_README.md** - Complete feature documentation
- **FINANCE_MODULE_QUICKSTART.md** - Setup and installation guide  
- **FINANCE_MODULE_IMPLEMENTATION_SUMMARY.md** - Overview of deliverables
- **FINANCE_MODULE_CHECKLIST.md** - Implementation verification
- **FINANCE_MODULE_DEVELOPER_REFERENCE.md** - This file

## Quick Setup Reference

```bash
# 1. Create migrations (see QUICKSTART guide)
php artisan migrate

# 2. Seed data (optional)
php artisan db:seed --class=FinanceSeeder

# 3. Build assets
npm run dev

# 4. Start server
php artisan serve

# 5. Test
# Visit: http://localhost:8000/finance/dashboard
```

## Common Errors & Solutions

| Error | Solution |
|-------|----------|
| Route not found (404) | Clear route cache: `php artisan route:clear` |
| Model not found | Check namespace: should be `App\Models\ModelName` |
| Component not rendering | Check Vue import paths and component registration |
| TypeScript error | Verify interface import and type definition |
| Database error | Run `php artisan migrate` to create tables |

## Links & Resources

- Laravel Docs: https://laravel.com/docs/12.x
- Vue 3 Docs: https://vuejs.org
- Inertia.js Docs: https://inertiajs.com
- Tailwind CSS Docs: https://tailwindcss.com

---

**Last Updated**: February 19, 2026
**Version**: 1.0
**For**: Finance Module Developers
