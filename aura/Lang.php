<?php

namespace app\aura;

/**
 * Class Lang
 *
 * Resolves the active locale from the APP_LOCALE environment variable
 * and returns locale-specific messages with :placeholder substitution.
 *
 * This class does not hardcode which locales exist. A locale is available
 * as soon as a matching message catalog class can be found by naming
 * convention: for locale "xx", the class "app\config\Messages" . StudlyCase("xx")
 * must exist with a public VALIDATOR const array.
 *
 *   config/messages_en.php -> app\config\MessagesEn
 *   config/messages_ja.php -> app\config\MessagesJa
 *   config/messages_fr.php -> app\config\MessagesFr (example: adding French
 *                             requires only this one new file under config/;
 *                             this class never needs to change)
 */
class Lang {
    public const DEFAULT_LOCALE = 'en';

    /**
     * Resolve the active locale from $_ENV['APP_LOCALE'].
     * Falls back to the default locale when unset, or when no message
     * catalog class exists for the requested locale.
     *
     * @return string
     */
    public static function locale(): string {
        $locale = $_ENV['APP_LOCALE'] ?? self::DEFAULT_LOCALE;

        return self::catalogClass($locale) !== null
            ? $locale
            : self::DEFAULT_LOCALE;
    }

    /**
     * Get a validation message by catalog key, with :placeholder substitution.
     *
     * @param string $key Catalog key, e.g. 'REQUIRED'.
     * @param array<string, mixed> $replace Map of placeholder name => value, e.g. ['field' => 'email'].
     * @return string
     */
    public static function get(string $key, array $replace = []): string {
        $className = self::catalogClass(self::locale());

        $messages = $className !== null ? $className::VALIDATOR : [];
        $message = $messages[$key] ?? $key;

        foreach ($replace as $placeholder => $value) {
            $message = \str_replace(':' . $placeholder, (string)$value, $message);
        }

        return $message;
    }

    /**
     * Resolve the fully-qualified message catalog class name for a locale,
     * e.g. 'en' -> app\config\MessagesEn, 'fr' -> app\config\MessagesFr.
     *
     * Returns null when no such class (and therefore no catalog file) exists,
     * so new locales can be added purely by dropping a new file under config/
     * without ever touching this class.
     *
     * @param string $locale
     * @return string|null
     */
    private static function catalogClass(string $locale): ?string {
        $studly = \str_replace('_', '', \ucwords($locale, '_'));
        $className = 'app\\config\\Messages' . $studly;

        return (\class_exists($className) && \defined($className . '::VALIDATOR'))
            ? $className
            : null;
    }
}
