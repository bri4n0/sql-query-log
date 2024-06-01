# Laravel SQL query log
<img src="https://raw.githubusercontent.com/bri4n0/sql-query-log/main/screenshot.png">

## Install

To install via Composer, run:

``` bash
composer require bri4n0/sql-query-log
```

For version <= 11:
Add to section providers of config/app.php:
```php
// config/app.php
'providers' => [
    ...
    Bri4n0\SqlQueryLog\DataBaseQueryServiceProvider::class,
];
```
For version >= 11:
Add to section providers of bootstrap/providers.php:
```php
// bootstrap/providers.php
return [
    ...
    Bri4n0\SqlQueryLog\DataBaseQueryServiceProvider::class,
];
```

Set enable SQL query log, Add to `.env`:

```bash
ENABLE_SQL_LOG=true
```

### Others
#### Custom logging channel:

1. open file config/logging.php
2. add channel to section channels:
```php
    'channels' => [
    ...,
     'sql-query-log' => [
            'driver'               => 'daily',
            'path'                 => storage_path('logs/queries.log'),
            'days'                 => env('LOG_DAILY_DAYS', 14),
            'replace_placeholders' => true,
        ],
    ]
```
3. enable using sql query log channel:

```bash
SQL_LOG_CHANNEL=sql-query-log
```
#### Custom logging channel level:
```bash
SQL_LOG_LEVEL=debug
```
## License
[MIT](LICENSE) © Bri4n0
