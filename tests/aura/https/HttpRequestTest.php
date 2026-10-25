<?php

use PHPUnit\Framework\TestCase;
use app\aura\https\HttpRequest;

class HttpRequestTest extends TestCase
{
    private array $server;
    private array $get;
    private array $post;

    protected function setUp(): void
    {
        $this->server = $_SERVER;
        $this->get = $_GET;
        $this->post = $_POST;
    }

    protected function tearDown(): void
    {
        $_SERVER = $this->server;
        $_GET = $this->get;
        $_POST = $this->post;
    }

    public function test_get_method_returns_lowercase_method()
    {
        $_SERVER['REQUEST_METHOD'] = 'PUT';

        $this->assertSame('put', (new HttpRequest())->getMethod());
    }

    public function test_post_with_method_field_is_overridden()
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_POST = ['_method' => 'DELETE'];

        $this->assertSame('delete', (new HttpRequest())->getMethod());
    }

    public function test_unknown_method_field_is_ignored()
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_POST = ['_method' => 'GET'];

        $this->assertSame('post', (new HttpRequest())->getMethod());
    }

    public function test_method_field_is_ignored_for_get()
    {
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_POST = ['_method' => 'DELETE'];

        $this->assertSame('get', (new HttpRequest())->getMethod());
    }

    public function test_body_excludes_query_string()
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';
        unset($_SERVER['CONTENT_TYPE']);
        $_GET = ['csrf_token' => 'from-url'];
        $_POST = ['title' => 'hello'];

        $body = (new HttpRequest())->body();

        $this->assertSame(['title' => 'hello'], $body);
        $this->assertArrayNotHasKey('csrf_token', $body);
    }
}
