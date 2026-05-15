<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    public function test_core_auth_and_alumni_routes_are_registered(): void
    {
        $this->assertTrue(Route::has('login'));
        $this->assertTrue(Route::has('password.reset.verify_form'));
        $this->assertTrue(Route::has('google-login'));
        $this->assertTrue(Route::has('facebook-login'));

        $this->assertTrue(Route::has('home'));
        $this->assertTrue(Route::has('checkout'));
        $this->assertTrue(Route::has('checkout.success'));
        $this->assertTrue(Route::has('pay'));
        $this->assertTrue(Route::has('transaction.list'));
        $this->assertTrue(Route::has('chats.index'));
        $this->assertTrue(Route::has('chats.single_user_chat'));
        $this->assertTrue(Route::has('chats.send_message'));
    }

    public function test_payment_callback_route_keeps_api_prefix_without_addon_callbacks(): void
    {
        $this->assertTrue(Route::has('payment.verify'));
        $this->assertSame('api/verify', Route::getRoutes()->getByName('payment.verify')->uri());
    }
}
