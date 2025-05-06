<?php
declare(strict_types=1);

/**
 * Wachey Report Package
 *
 * @package Wachey\Api
 * @author  Pisaroni Alberto
 * @link    https://github.com/wachey-com/wachey-php
 */

namespace Wachey\Api;

class Report
{
    
    /**
     * Mappa chiavi env → config Laravel.
     */
    private const CONFIG_MAP = [
        'WACHEY_API_KEY'   => 'services.wachey.key',
        'WACHEY_PASSWORD'  => 'services.wachey.password',
        'APP_ENV'          => 'app.env',
    ];

    /**
     * Carica manualmente le variabili d’ambiente da un file .env.
     *
     * @param  string  $path  Percorso completo al file .env
     * @return void
     * @throws \RuntimeException Se il file non esiste
     */
    private static function loadEnv(string $path): void
    {
        if (! file_exists($path)) {
            throw new \RuntimeException(".env file not found at: {$path}");
        }

        $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            $trimmed = trim($line);
            if ($trimmed === '' || str_starts_with($trimmed, '#') || ! str_contains($trimmed, '=')) {
                continue;
            }

            [$key, $value] = explode('=', $trimmed, 2);
            $key   = trim($key);
            $value = trim($value);

            if (! array_key_exists($key, $_ENV)) {
                putenv("{$key}={$value}");
                $_ENV[$key] = $value;
            }
        }
    }

    /**
     * Recupera una variabile d’ambiente: prima da config(), poi da .env.
     *
     * @param  string      $key      Chiave in $_ENV o in CONFIG_MAP
     * @param  string|null $default  Valore di default
     * @return string|null
     */
    private static function getEnvVariable(string $key, ?string $default = null): ?string
    {
        // 1) Laravel config()
        if (function_exists('config') && isset(self::CONFIG_MAP[$key])) {
            $configValue = config(self::CONFIG_MAP[$key]);
            if ($configValue !== null) {
                return (string) $configValue;
            }
        }

        // 2) Fallback: carica da .env manualmente
        $envPath = $_SERVER['DOCUMENT_ROOT'] . '/.env';
        if (! isset($_ENV[$key])) {
            self::loadEnv($envPath);
        }

        $val = getenv($key);
        return $val !== false ? $val : $default;
    }

    /**
     * Invia un report di errore a Wachey API.
     *
     * @param  string|null $error  Messaggio di errore
     * @param  string|null $path   File o rotta in cui è avvenuto
     * @param  int|null    $line   Linea di codice
     * @param  string|null $ip     Indirizzo IP
     * @param  string|null $user   Identificativo utente
     * @return \stdClass|false     Risposta decodificata o false in caso di JSON non valido
     * @throws \RuntimeException   Se cURL fallisce o file .env non trovato
     */
    public static function error(
        ?string $error = null,
        ?string $path  = null,
        ?int    $line  = null,
        ?string $ip    = null,
        ?string $user  = null
    ) {
        // Prepara i dati
        $payload = [
            'api_key'  => self::getEnvVariable('WACHEY_API_KEY'),
            'password' => self::getEnvVariable('WACHEY_PASSWORD'),
            'error'    => $error,
            'path'     => $path,
            'line'     => $line,
            'ip'       => $ip,
            'user'     => $user,
            'env'      => self::getEnvVariable('APP_ENV', 'production'),
        ];

        // Invia via cURL
        $ch = curl_init('https://api.wachey.com/report/error');
        curl_setopt_array($ch, [
            CURLOPT_POST           => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POSTFIELDS     => $payload,
        ]);

        $response = curl_exec($ch);
        if ($response === false) {
            $err = curl_error($ch);
            curl_close($ch);
            throw new \RuntimeException("cURL error: {$err}");
        }
        curl_close($ch);

        $decoded = json_decode($response);
        return json_last_error() === JSON_ERROR_NONE ? $decoded : false;
    }
}
