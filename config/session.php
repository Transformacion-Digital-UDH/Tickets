<?php

use Illuminate\Support\Str;

return [

    'driver' => env('SESSION_DRIVER', 'database'), // Asegúrate de que sea 'file' o 'database' según lo necesites

    'lifetime' => env('SESSION_LIFETIME', 120), // Ajusta según lo que consideres adecuado

    'expire_on_close' => env('SESSION_EXPIRE_ON_CLOSE', false), // Cambiar a true si quieres que expire al cerrar el navegador

    'encrypt' => env('SESSION_ENCRYPT', false), // Establecer en true si deseas encriptar la sesión

    'files' => storage_path('framework/sessions'), // Asegúrate de que esta carpeta sea escribible

    'connection' => env('SESSION_CONNECTION'), // Revisa tu conexión si usas base de datos

    'table' => env('SESSION_TABLE', 'sessions'), // Asegúrate de que esta tabla exista si usas base de datos

    'store' => env('SESSION_STORE'), // Define el almacenamiento si usas caché

    'lottery' => [2, 100], // Puede ajustarse según la necesidad

    'cookie' => env('SESSION_COOKIE', Str::slug(env('APP_NAME', 'laravel'), '_').'_session'),

    'path' => env('SESSION_PATH', '/'), // Ajusta si necesitas un camino específico

    'domain' => env('SESSION_DOMAIN'), // Deja vacío para que esté disponible en todos los subdominios

    'secure' => env('SESSION_SECURE_COOKIE', false), // Cambia a true en producción con HTTPS

    'http_only' => env('SESSION_HTTP_ONLY', true), // Mantener en true para mayor seguridad

    'same_site' => env('SESSION_SAME_SITE', null), // 'lax' es una buena opción por defecto

    'partitioned' => env('SESSION_PARTITIONED_COOKIE', false), // Cambia a true si es necesario para cookies particionadas

];
