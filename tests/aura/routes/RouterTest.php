<?php

use PHPUnit\Framework\TestCase;
use app\aura\https\HttpRequest;
use app\aura\routes\Router;

class RouterTest extends TestCase
{
    private array $server;
    private array $post;

    protected function setUp(): void
    {
        $this->server = $_SERVER;
        $this->post = $_POST;
        $_SERVER['SCRIPT_NAME'] = '/ghostphp/index.php';
        unset($_SERVER['CONTENT_TYPE']);
        $_POST = [];
    }

    protected function tearDown(): void
    {
        $_SERVER = $this->server;
        $_POST = $this->post;
        http_response_code(200);
    }

    private function dispatch(string $method, string $path, Router $router): string
    {
        $_SERVER['REQUEST_METHOD'] = $method;
        $_SERVER['REQUEST_URI'] = '/ghostphp/' . $path;

        ob_start();
        $router->resolve();
        return ob_get_clean();
    }

    private function router(): Router
    {
        $router = new Router(new HttpRequest());
        $router->get('item',    fn () => 'get');
        $router->put('item',    fn () => 'put');
        $router->patch('item',  fn () => 'patch');
        $router->delete('item', fn () => 'delete');
        $router->post('form',   fn () => 'post');
        return $router;
    }

    public function test_put_patch_delete_routes_are_dispatched()
    {
        $this->assertSame('put',    $this->dispatch('PUT',    'item', $this->router()));
        $this->assertSame('patch',  $this->dispatch('PATCH',  'item', $this->router()));
        $this->assertSame('delete', $this->dispatch('DELETE', 'item', $this->router()));
    }

    public function test_method_field_dispatches_to_delete_route()
    {
        $_POST = ['_method' => 'DELETE'];

        $this->assertSame('delete', $this->dispatch('POST', 'item', $this->router()));
    }

    public function test_head_is_handled_as_get()
    {
        $this->assertSame('get', $this->dispatch('HEAD', 'item', $this->router()));
    }

    public function test_wrong_method_on_known_path_returns_405()
    {
        $this->assertSame('Method Not Allowed', $this->dispatch('DELETE', 'form', $this->router()));
        $this->assertSame(405, http_response_code());
    }

    public function test_unknown_path_still_returns_404()
    {
        $this->assertSame('Not found', $this->dispatch('GET', 'nothing', $this->router()));
        $this->assertSame(404, http_response_code());
    }
}
