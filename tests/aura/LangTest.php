<?php

use PHPUnit\Framework\TestCase;
use app\aura\Lang;

class LangTest extends TestCase
{
    private ?string $previousLocale = null;
    private bool $hadPreviousLocale = false;

    protected function setUp(): void
    {
        $this->hadPreviousLocale = array_key_exists('APP_LOCALE', $_ENV);
        $this->previousLocale = $_ENV['APP_LOCALE'] ?? null;
    }

    protected function tearDown(): void
    {
        if ($this->hadPreviousLocale) {
            $_ENV['APP_LOCALE'] = $this->previousLocale;
        } else {
            unset($_ENV['APP_LOCALE']);
        }
    }

    public function test_locale_defaults_to_en_when_unset()
    {
        unset($_ENV['APP_LOCALE']);

        $this->assertSame('en', Lang::locale());
    }

    public function test_locale_falls_back_to_en_for_unsupported_value()
    {
        $_ENV['APP_LOCALE'] = 'fr';

        $this->assertSame('en', Lang::locale());
    }

    public function test_locale_returns_ja_when_set()
    {
        $_ENV['APP_LOCALE'] = 'ja';

        $this->assertSame('ja', Lang::locale());
    }

    public function test_get_returns_english_message_by_default()
    {
        unset($_ENV['APP_LOCALE']);

        $this->assertSame(
            'email is required',
            Lang::get('REQUIRED', ['field' => 'email'])
        );
    }

    public function test_get_returns_japanese_message_when_locale_is_ja()
    {
        $_ENV['APP_LOCALE'] = 'ja';

        $this->assertSame(
            'email は必須項目です。',
            Lang::get('REQUIRED', ['field' => 'email'])
        );
    }

    public function test_get_substitutes_multiple_placeholders()
    {
        unset($_ENV['APP_LOCALE']);

        $this->assertSame(
            'Value must be between 1 and 5',
            Lang::get('BETWEEN', ['min' => 1, 'max' => 5])
        );
    }

    public function test_get_falls_back_to_key_when_missing_from_catalog()
    {
        unset($_ENV['APP_LOCALE']);

        $this->assertSame('NOT_A_REAL_KEY', Lang::get('NOT_A_REAL_KEY'));
    }
}
