<?php

namespace Framework\Support;

class Lang
{
    protected static $locale;
    protected static $fallbackLocale;
    protected static $translations = [];

    public static function setLocale(string $locale): void
    {
        self::$locale = $locale;
    }

    public static function setFallbackLocale(string $locale): void
    {
        self::$fallbackLocale = $locale;
    }

    public static function get(string $key, array $replace = [], string $locale = null): string
    {
        $locale = $locale ?? self::$locale;

        list($file, $stringKey) = explode('.', $key, 2);

        self::load($file, $locale);

        $translation = self::$translations[$locale][$file][$stringKey] ?? null;

        if (!$translation && self::$fallbackLocale) {
            self::load($file, self::$fallbackLocale);
            $translation = self::$translations[self::$fallbackLocale][$file][$stringKey] ?? $key;
        }

        return self::replacePlaceholders($translation, $replace);
    }

    protected static function load(string $file, string $locale): void
    {
        if (isset(self::$translations[$locale][$file])) {
            return;
        }

        $path = config('app.lang_path', __DIR__ . '/../../lang') . "/{$locale}/{$file}.php";

        if (file_exists($path)) {
            self::$translations[$locale][$file] = require $path;
        }
    }

    protected static function replacePlaceholders(string $translation, array $replace): string
    {
        foreach ($replace as $key => $value) {
            $translation = str_replace(':' . $key, $value, $translation);
        }

        return $translation;
    }
}
