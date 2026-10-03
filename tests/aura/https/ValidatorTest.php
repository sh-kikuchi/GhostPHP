<?php

use PHPUnit\Framework\TestCase;
use app\aura\https\Validator;

class ValidatorTest extends TestCase
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

    public function test_required_message_in_english_by_default()
    {
        unset($_ENV['APP_LOCALE']);

        $validator = new Validator();
        $validator->required('', 'email');

        $this->assertSame(
            [['email' => 'email is required']],
            $validator->getErrors()
        );
    }

    public function test_required_message_in_japanese_when_locale_is_ja()
    {
        $_ENV['APP_LOCALE'] = 'ja';

        $validator = new Validator();
        $validator->required('', 'email');

        $this->assertSame(
            [['email' => 'email は必須項目です。']],
            $validator->getErrors()
        );
    }

    public function test_min_length_message_localized()
    {
        $_ENV['APP_LOCALE'] = 'ja';

        $validator = new Validator();
        $validator->minLength('ab', 'name', 5);

        $this->assertSame(
            [['name' => '最小文字数は5文字です。']],
            $validator->getErrors()
        );
    }

    public function test_password_confirm_message_localized()
    {
        $_ENV['APP_LOCALE'] = 'ja';

        $validator = new Validator();
        $validator->passwordConfirm('abcdefgh', 'different');

        $this->assertSame(
            [['password_conf' => 'パスワードと確認用パスワードが一致しません。']],
            $validator->getErrors()
        );
    }

    public function test_between_message_localized_with_placeholders()
    {
        $_ENV['APP_LOCALE'] = 'ja';

        $validator = new Validator();
        $validator->between(10, 'age', 1, 5);

        $this->assertSame(
            [['age' => '1 から 5 の間の値を入力してください。']],
            $validator->getErrors()
        );
    }

    public function test_custom_message_overrides_locale_catalog_in_english()
    {
        unset($_ENV['APP_LOCALE']);

        $validator = new Validator();
        $validator->setCustomMessages(['email' => 'Custom!']);
        $validator->mailFormat('not-an-email', 'email');

        $this->assertSame(
            [['email' => 'Custom!']],
            $validator->getErrors()
        );
    }

    public function test_custom_message_overrides_locale_catalog_in_japanese()
    {
        $_ENV['APP_LOCALE'] = 'ja';

        $validator = new Validator();
        $validator->setCustomMessages(['email' => 'Custom!']);
        $validator->mailFormat('not-an-email', 'email');

        $this->assertSame(
            [['email' => 'Custom!']],
            $validator->getErrors()
        );
    }

    public function test_has_file_fails_when_no_file_selected()
    {
        unset($_ENV['APP_LOCALE']);

        $validator = new Validator();
        $validator->hasFile(['error' => [UPLOAD_ERR_NO_FILE]], 'files');

        $this->assertSame(
            [['files' => 'files is required']],
            $validator->getErrors()
        );
    }

    public function test_has_file_passes_with_uploaded_file()
    {
        $validator = new Validator();
        $validator->hasFile(['error' => [UPLOAD_ERR_OK]], 'files');

        $this->assertSame([], $validator->getErrors());
    }
}
