<?php

// config/app.php

return [
    'debug' => true, // Set to false in production
    'cache_enabled' => false, // Enable/disable view caching
    'cache_lifetime' => 3600, // Cache lifetime in seconds (1 hour)
    'storage_path' => __DIR__ . '/../storage',
    'lang_path' => __DIR__ . '/../../lang',
    'locale' => 'en',
    'fallback_locale' => 'en',
    'user_model' => App\Models\User::class,
];