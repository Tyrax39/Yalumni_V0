<?php

namespace Tests\Feature;

use App\Http\Middleware\AddonMiddleware;
use App\Http\Middleware\InstallMiddleware;
use App\Http\Middleware\VersionUpdate;
use App\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Route as LaravelRoute;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class AuthWorkflowContractTest extends TestCase
{
    public function test_login_and_registration_routes_keep_expected_contracts(): void
    {
        $this->assertRouteAccepts('GET', 'login', 'login');
        $this->assertRouteAccepts('POST', 'login');
        $this->assertRouteAccepts('POST', 'logout', 'logout');
        $this->assertRouteAccepts('GET', 'logout');
        $this->assertRouteAccepts('GET', 'register', 'register');
        $this->assertRouteAccepts('POST', 'register');
        $this->assertTrue(Route::has('google-login'));
        $this->assertTrue(Route::has('facebook-login'));
    }

    public function test_password_reset_routes_keep_custom_otp_contracts(): void
    {
        $this->assertRouteAccepts('GET', 'password/reset', 'password.request');
        $this->assertRouteAccepts('POST', 'password/email', 'password.email');
        $this->assertRouteAccepts('GET', 'password/reset/verify/{token}/{email}', 'password.reset.verify_form');
        $this->assertRouteAccepts('GET', 'password/reset/verify/{token}', 'password.reset.verify');
        $this->assertRouteAccepts('POST', 'password/reset/verify-resend/{token}', 'password.reset.verify_resend');
        $this->assertRouteAccepts('POST', 'password/reset/update/{token}', 'password.update');
    }

    public function test_login_rejects_missing_credentials_before_authentication_attempt(): void
    {
        $this->withoutOperationalGatekeepers();

        $response = $this->from('/login')->post('/login', []);

        $response->assertRedirect('http://localhost/login');
        $response->assertSessionHasErrors(['email', 'password']);
    }

    public function test_password_reset_request_rejects_missing_email_before_lookup(): void
    {
        $this->withoutOperationalGatekeepers();

        $response = $this->from('/password/reset')->post('/password/email', []);

        $response->assertRedirect('http://localhost/password/reset');
        $response->assertSessionHasErrors(['email']);
    }

    public function test_app_admin_and_super_admin_routes_redirect_guests_to_login(): void
    {
        $this->withoutOperationalGatekeepers();

        $this->get('/home')->assertRedirect(route('login'));
        $this->get('/admin/dashboard')->assertRedirect(route('login'));
        $this->get('/super-admin/dashboard')->assertRedirect(route('login'));
    }

    private function assertRouteAccepts(string $method, string $uri, ?string $name = null): void
    {
        $route = $this->routeFor($method, $uri);

        $this->assertNotNull($route, "Expected [{$method}] route [{$uri}] to be registered.");

        if ($name !== null) {
            $this->assertSame($name, $route->getName());
        }
    }

    private function routeFor(string $method, string $uri): ?LaravelRoute
    {
        foreach (Route::getRoutes()->getRoutesByMethod()[$method] ?? [] as $route) {
            if ($route->uri() === $uri) {
                return $route;
            }
        }

        return null;
    }

    private function withoutOperationalGatekeepers(): void
    {
        config()->set('app.url', 'http://localhost');
        URL::forceRootUrl('http://localhost');

        $this->withoutMiddleware([
            AddonMiddleware::class,
            InstallMiddleware::class,
            VerifyCsrfToken::class,
            VersionUpdate::class,
        ]);
    }
}
