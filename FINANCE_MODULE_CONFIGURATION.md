# Finance Module - Configuration Guide

## Environment Setup

### Required Environment Variables

Add these to your `.env` file:

```env
# Database Configuration (already in Laravel default)
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_database_name
DB_USERNAME=root
DB_PASSWORD=

# App Configuration
APP_NAME="Your App Name"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000

# Finance Module Settings (Optional)
FINANCE_CURRENCY=USD
FINANCE_DECIMAL_PLACES=2
FINANCE_DATE_FORMAT=M d, Y
FINANCE_ENABLE_AUDIT_LOG=false
```

### Environment-Specific Configuration

#### Development Environment
```env
APP_ENV=local
APP_DEBUG=true
FINANCE_ENABLE_AUDIT_LOG=false  # Disable for performance
```

#### Production Environment
```env
APP_ENV=production
APP_DEBUG=false
FINANCE_ENABLE_AUDIT_LOG=true  # Enable for compliance
FINANCE_BACKUP_ENABLED=true
```

## Database Configuration

### MySQL Configuration

Ensure your MySQL server supports:
- **Minimum Version**: 5.7 (recommend 8.0+)
- **Encoding**: utf8mb4
- **Collation**: utf8mb4_unicode_ci

**my.ini or my.cnf settings:**
```ini
[mysqld]
character-set-server=utf8mb4
collation-server=utf8mb4_unicode_ci
secure_file_priv=
```

### Database User Permissions

Create a finance-specific database user (optional):

```sql
CREATE USER 'finance_user'@'localhost' IDENTIFIED BY 'secure_password';
GRANT SELECT, INSERT, UPDATE, DELETE, CREATE, INDEX ON your_database.* TO 'finance_user'@'localhost';
FLUSH PRIVILEGES;
```

## Laravel Configuration Files

### config/database.php

The Finance Module uses the default database connection. Ensure it's properly configured:

```php
'mysql' => [
    'driver' => 'mysql',
    'host' => env('DB_HOST', '127.0.0.1'),
    'port' => env('DB_PORT', '3306'),
    'database' => env('DB_DATABASE', 'forge'),
    'username' => env('DB_USERNAME', 'forge'),
    'password' => env('DB_PASSWORD', ''),
    'unix_socket' => env('DB_SOCKET', ''),
    'charset' => 'utf8mb4',
    'collation' => 'utf8mb4_unicode_ci',
    'prefix' => '',
    'strict' => true,
    'engine' => null,
],
```

### config/auth.php

Finance Module uses Laravel's default authentication:

```php
'guards' => [
    'web' => [
        'driver' => 'session',
        'provider' => 'users',
    ],
],
```

### config/app.php

Timezone configuration (important for date calculations):

```php
'timezone' => 'UTC', // Change to your timezone
```

## Middleware Configuration

### Authentication Middleware

Finance routes use `auth` middleware. Verify in `app/Http/Middleware/Authenticate.php`:

```php
protected $routeMiddleware = [
    'auth' => \App\Http\Middleware\Authenticate::class,
    // ...
];
```

### Add Role-Based Access (Optional)

To restrict Finance Module to specific roles:

1. Create a Role middleware:
```bash
php artisan make:middleware CheckFinanceRole
```

2. Update `app/Http/Middleware/CheckFinanceRole.php`:
```php
public function handle(Request $request, Closure $next)
{
    if (!auth()->user() || !auth()->user()->hasRole('finance_admin')) {
        abort(403, 'Unauthorized');
    }
    return $next($request);
}
```

3. Register in `app/Http/Kernel.php`:
```php
protected $routeMiddleware = [
    // ...
    'finance.admin' => \App\Http\Middleware\CheckFinanceRole::class,
];
```

4. Update routes in `routes/web.php`:
```php
Route::middleware(['auth', 'finance.admin'])->prefix('finance')->group(function () {
    // Finance routes
});
```

## Caching Configuration

### Enable Query Caching (Recommended)

For large reports, enable caching in `config/cache.php`:

```php
'default' => env('CACHE_DRIVER', 'file'),

'stores' => [
    'file' => [
        'driver' => 'file',
        'path' => storage_path('framework/cache/data'),
    ],
    'database' => [
        'driver' => 'database',
        'table' => 'cache',
        'prefix' => env('CACHE_PREFIX', ''),
    ],
    'redis' => [
        'driver' => 'redis',
        'connection' => 'cache',
    ],
],
```

### Cache Reports Example

In `FinanceReportController.php`:
```php
public function generateReport(Request $request)
{
    $cacheKey = 'finance.report.' . $request->get('type') . '.' . $request->get('start_date');
    
    $report = cache()->remember($cacheKey, 3600, function () use ($request) {
        // Generate report
        return $this->calculate();
    });
    
    return $report;
}
```

## File & Storage Configuration

### config/filesystems.php

For future PDF and export features:

```php
'disks' => [
    'local' => [
        'driver' => 'local',
        'root' => storage_path('app'),
        'url' => env('APP_URL') . '/storage',
        'visibility' => 'private',
    ],
    'public' => [
        'driver' => 'local',
        'root' => storage_path('app/public'),
        'url' => env('APP_URL') . '/storage',
        'visibility' => 'public',
    ],
    'finance_exports' => [
        'driver' => 'local',
        'root' => storage_path('app/finance/exports'),
        'visibility' => 'private',
    ],
],
```

## Logging Configuration

### config/logging.php

Configure Finance-specific logging:

```php
'channels' => [
    'finance' => [
        'driver' => 'single',
        'path' => storage_path('logs/finance.log'),
        'level' => env('LOG_LEVEL', 'debug'),
    ],
],
```

### Log Finance Activities

In controller:
```php
use Illuminate\Support\Facades\Log;

Log::channel('finance')->info('Transaction created', [
    'transaction_id' => $transaction->id,
    'user_id' => auth()->id(),
    'amount' => $transaction->amount,
]);
```

## Email Configuration (Future)

For automated invoice delivery:

```env
MAIL_DRIVER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your_username
MAIL_PASSWORD=your_password
MAIL_FROM_ADDRESS=noreply@finance.app
MAIL_FROM_NAME="Finance Module"
```

## Queue Configuration (Future)

For background processing:

```env
QUEUE_CONNECTION=database
# or
QUEUE_CONNECTION=redis
```

## Session Configuration

### config/session.php

Finance-specific session settings:

```php
'lifetime' => 120, // 2 hours for sensitive financial data
'expire_on_close' => true,
'encrypt' => true, // Enable session encryption
```

## CORS Configuration (If API Endpoints Used)

### config/cors.php

```php
'paths' => ['api/*', 'finance/*'],
'allowed_methods' => ['*'],
'allowed_origins' => [env('APP_URL')],
'allowed_origins_patterns' => [],
'allowed_headers' => ['*'],
'exposed_headers' => [],
'max_age' => 0,
'supports_credentials' => true,
```

## Security Headers

### Add to Web Middleware

In `app/Http/Middleware/EncryptCookies.php` or create new middleware:

```php
protected function after(Request $request, Response $response)
{
    $response->headers->set('X-Content-Type-Options', 'nosniff');
    $response->headers->set('X-Frame-Options', 'DENY');
    $response->headers->set('X-XSS-Protection', '1; mode=block');
    $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubdomains');
    
    return $response;
}
```

## Nginx Configuration (Production)

```nginx
location /finance {
    try_files $uri $uri/ /index.php?$query_string;
}

# Prevent access to sensitive files
location ~ /\. {
    deny all;
}

location ~* \.(env|log|sql)$ {
    deny all;
}
```

## Apache Configuration (Production)

In `.htaccess`:

```apache
<Files ".env">
    <IfModule mod_authz_core.c>
        Require all denied
    </IfModule>
    <IfModule !mod_authz_core.c>
        Order allow,deny
        Deny from all
    </IfModule>
</Files>

<FilesMatch "\.log$">
    <IfModule mod_authz_core.c>
        Require all denied
    </IfModule>
    <IfModule !mod_authz_core.c>
        Order allow,deny
        Deny from all
    </IfModule>
</FilesMatch>
```

## Performance Tuning

### Database Query Optimization

Add indexes in migration:

```php
$table->index('date');
$table->index('type');
$table->index('status');
$table->index('due_date');
```

Monitor slow queries:

```php
// In config/database.php
'mysql' => [
    // ...
    'log_query_time' => 1000, // Log queries > 1 second
],
```

### Request Caching

For frequently generated reports:

```php
HTTP::cacheFor(3600)->get(...);
// Or use Laravel's cache facade
Cache::put('report.key', $data, 3600);
```

### Database Connection Pooling

For high-traffic scenarios, use:

```env
DB_CONNECTION=mysql
# Consider using connection pooling with tools like ProxySQL
```

## Backup Configuration

For financial data safety:

```bash
# Create scheduled backup in app/Console/Kernel.php
$schedule->command('db:backup')
    ->daily()
    ->at('02:00')
    ->emailOutputOnFailure('admin@example.com');
```

## Regular Maintenance

### Weekly Tasks
```bash
# Clear cache
php artisan cache:clear

# Clear logs older than 30 days (create custom command)
php artisan logs:clear
```

### Monthly Tasks
```bash
# Archive old transactions (optional)
# Backup database (crucial!)
# Review security logs
# Check for updates: composer update
```

## Development vs Production

### Development Configuration
```env
APP_ENV=local
APP_DEBUG=true
DATABASE_SLOW_LOG=true
MAIL_DRIVER=log  # Send emails to log
```

### Production Configuration
```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-domain.com
ASSET_URL=https://your-domain.com/public
CACHE_DRIVER=redis
SESSION_DRIVER=database
MAIL_DRIVER=smtp
```

## Troubleshooting Configuration

### Issue: Routes not working
**Solution:**
```bash
php artisan route:clear
php artisan cache:clear
php artisan config:cache
```

### Issue: Database connection error
**Solution:**
1. Verify `.env` database credentials
2. Check MySQL is running: `mysql -u root -p`
3. Create database if missing: `CREATE DATABASE your_database;`

### Issue: Permission denied errors
**Solution:**
```bash
chmod -R 755 storage bootstrap
chmod -R 777 storage bootstrap/cache
```

### Issue: Session errors
**Solution:**
1. Check `storage/framework/sessions` directory exists
2. Ensure writable permissions: `chmod -R 777 storage`

## Configuration Checklist

- [ ] `.env` file created with database credentials
- [ ] Database created and accessible
- [ ] Encryption key generated: `php artisan key:generate`
- [ ] Database config verified in `config/database.php`
- [ ] Timezone set in `config/app.php`
- [ ] Authentication configured in `config/auth.php`
- [ ] Cache driver set appropriately
- [ ] Session configuration verified
- [ ] File permissions set (storage 777)
- [ ] Mail config set (if needed for features)
- [ ] Logging configured
- [ ] Security headers configured
- [ ] HTTPS enabled (production)
- [ ] Backups scheduled

## Next Steps

1. Configure your `.env` file based on your environment
2. Run `php artisan migrate` to create tables
3. Test the Finance Module at `/finance/dashboard`
4. Configure optional features (email, PDF export, etc.)
5. Set up regular backups

---

**Version**: 1.0
**Last Updated**: February 19, 2026
**For**: Finance Module Configuration
