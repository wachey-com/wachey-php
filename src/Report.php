<?php
namespace Wachey\Api;

class Report {

    // Method to load environment variables manually
    private static function loadEnv($path) {
        if (!file_exists($path)) {
            throw new \Exception(".env file not found at: $path");
        }

        $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            if (strpos(trim($line), '#') === 0) {
                continue;
            }

            list($key, $value) = explode('=', $line, 2);
            $key = trim($key);
            $value = trim($value);

            if (!array_key_exists($key, $_ENV)) {
                putenv("$key=$value");
                $_ENV[$key] = $value;
            }
        }
    }

    // Method to retrieve the environment variable depending on the context
    private static function getEnvVariable($key, $default = null) {
        if (function_exists('env')) {
            // We're likely in a Laravel environment
            return env($key, $default);
        } else {
            // Always load the .env file from public_html
            $publicHtmlPath = $_SERVER['DOCUMENT_ROOT'] . '/.env'; // Adjust this if needed

            // Load environment variables from public_html if not already loaded
            if (!isset($_ENV[$key])) {
                self::loadEnv($publicHtmlPath);
            }
            return getenv($key) ?: $default;
        }
    }

    public static function error($error = null, $path = null, $line = null, $ip = null, $user = null) {
        try {
            $curl = curl_init();

            curl_setopt($curl, CURLOPT_URL, 'https://api.wachey.com/report/error');
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

            $data = array(
                'api_key' => self::getEnvVariable('WACHEY_API_KEY'),
                'password' => self::getEnvVariable('WACHEY_PASSWORD'),
                'error' => $error,
                'path' => $path,
                'line' => $line,
                'ip' => $ip,
                'user' => $user,
                'env' => self::getEnvVariable('APP_ENV', 'production')
            );

            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);

            $response = curl_exec($curl);
            curl_close($curl);

            return json_decode($response);

        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }
}