# Wachey API PHP Client

The `Wachey\Api\Report` class provides a simple interface for reporting errors to the Wachey API. This class can be used in both **Laravel** and **non-Laravel** environments, with support for loading environment variables from a `.env` file located in the `public_html` folder.

## Features

- Supports both **Laravel** and **non-Laravel** environments.
- Automatically loads environment variables from the `.env` file in `public_html` for non-Laravel projects.
- Uses Laravel's `env()` function when running in a Laravel environment.
- Sends error reports to the Wachey API via `cURL`.

## Installation

1. Add the `Wachey\Api\Report` class to your PHP project.
2. Make sure you have a `.env` file in your `public_html` directory (or specify a custom path if needed).
3. In a non-Laravel environment, the `.env` file should contain your Wachey API credentials, such as:

    ```env
    WACHEY_API_KEY=your_wachey_api_key
    WACHEY_PASSWORD=your_wachey_password
    APP_ENV=production
    ```

4. In Laravel, use Laravel’s built-in `.env` handling mechanism.

## Usage

### In Laravel

#### Laravel 9 and Above

For automatic exception management in Laravel 9 and above, you can modify the `app.php` file.

Inside the `->withExceptions(function (Exceptions $exceptions) { ... })` section, add the following code:

```php
$exceptions->report(function (Throwable $e) {
    Report::error($e->getMessage(), $e->getFile(), $e->getLine(), request()->ip(), Auth::check() ? Auth::user()->email : null);
});
```

This will automatically send exceptions to the Wachey API.

#### Older Versions of Laravel

For older versions of Laravel, modify the `report()` method inside `app/Exceptions/Handler.php`:

Add the following snippet inside the `report()` method:

```php
Report::error($exception->getMessage(), $exception->getFile(), $exception->getLine(), request()->ip(), Auth::check() ? Auth::user()->email : null);
```

Ensure you also call the parent `report()` method:

```php
parent::report($exception);
```

This will automatically report exceptions when they are caught by Laravel.

### Manual Exception Reporting

If you prefer not to use automatic exception reporting, you can manually report exceptions within your application using `try-catch` blocks.

Wherever you expect exceptions, wrap your code in a `try-catch` and manually call `Report::error()` inside the `catch` block:

```php
try {
    // Your code here that may throw an exception
} catch (Exception $e) {
    Report::error($e->getMessage(), $e->getFile(), $e->getLine(), request()->ip(), Auth::check() ? Auth::user()->email : null);
}
```

This gives you control over exactly which exceptions are reported.

## Environment Variable Loading Logic

- **Laravel Projects:** If the `env()` function exists (as in a Laravel environment), the class will use it to retrieve environment variables.
  
- **Non-Laravel Projects:** In non-Laravel environments, the class will attempt to load the `.env` file from the `public_html` folder using PHP’s `putenv()` and `$_ENV`. Ensure your server’s document root is set correctly.

#### Example `.env` File:

```env
WACHEY_API_KEY=your_wachey_api_key
WACHEY_PASSWORD=your_wachey_password
APP_ENV=production
```

If your server doesn't use the `public_html` directory, you can adjust the path in the code or define a constant that points to your `.env` location.

## Configuration

By default, the `.env` file is expected to be in the `public_html` folder of your web server. If you are running a custom server configuration, make sure that the path to `public_html` is correctly set in the code, or define your own path:

```php
define('PUBLIC_HTML_PATH', '/path/to/your/public_html');
```

## License

This project is open-source and available under the [MIT License](LICENSE).