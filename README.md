# Wachey API PHP Client

A lightweight PHP client for sending error reports to the Wachey API, compatible with both **Laravel** and **vanilla PHP** projects.

> **Version:** 1.0.0
> **License:** MIT
> **Repository:** [https://github.com/wachey-com/wachey-php](https://github.com/wachey-com/wachey-php)

## Features

* **Laravel support** via `config()` / `env()`.
* **Non-Laravel support** with automatic loading of a `.env` file in `public_html`.
* Zero external dependencies (uses native cURL).
* Simple integration with exception handlers.

## Installation

```bash
composer require wachey/api
```

## Configuration

### In Laravel

1. In your `config/services.php`, add:

   ```php
   'wachey' => [
       'key'      => env('WACHEY_API_KEY'),
       'password' => env('WACHEY_PASSWORD'),
   ],
   ```

2. Make sure your `.env` includes:

   ```env
   WACHEY_API_KEY=your_api_key
   WACHEY_PASSWORD=your_password
   ```

### In Vanilla PHP

* Place a `.env` file in your `public_html/` directory containing:

  ```env
  WACHEY_API_KEY=your_api_key
  WACHEY_PASSWORD=your_password
  APP_ENV=production
  ```

* If your document root differs, define a constant **before** using the client:

  ```php
  ```

define('PUBLIC\_HTML\_PATH', '/path/to/your/public\_html');
\`\`\`

## Usage

### Automatic Exception Reporting in Laravel

In Laravel 9+ inside **`app/Exceptions/Handler.php`**, register a reportable callback:

```php
use Wachey\Api\Report;

public function register(): void
{
    $this->reportable(function (Throwable $e) {
        Report::error(
            $e->getMessage(),
            $e->getFile(),
            $e->getLine(),
            request()->ip(),
            optional(Auth::user())->email
        );
    });
}
```

### Manual Reporting

Wrap your code in a `try-catch` and call `Report::error()`:

```php
use Wachey\Api\Report;

try {
    // code that may throw
} catch (\Exception $e) {
    Report::error(
        $e->getMessage(),
        $e->getFile(),
        $e->getLine(),
        $_SERVER['REMOTE_ADDR'] ?? null,
        'optional_user_identifier'
    );
}
```

## API

```php
public static function error(
    ?string $error   = null,
    ?string $path    = null,
    ?int    $line    = null,
    ?string $ip      = null,
    ?string $user    = null
);
```

* **Returns**: `\stdClass` on success, or `false` if the JSON response is invalid.
* **Throws**: `\RuntimeException` on cURL errors or missing `.env`.

## Env Loading Logic

* **Laravel**: Uses `config('services.wachey.key')` and `config('services.wachey.password')`.
* **Non-Laravel**: Parses `.env` under `public_html` via `putenv()` and `$_ENV`.

Adjust the path as needed via a `PUBLIC_HTML_PATH` constant if you don’t use `public_html`.

## Project Structure

```
src/
└── Report.php
composer.json
README.md
LICENSE
```

## License

This project is released under the **MIT License**. See [LICENSE](LICENSE) for details.
