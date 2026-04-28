<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class AddonRouteRegistrationTest extends TestCase
{
    public function test_addon_bootstrap_checks_return_false_for_missing_or_unknown_addons(): void
    {
        $this->assertFalse(hasAddonBootstrapFiles('ALUSAAS'));
        $this->assertFalse(hasAddonBootstrapFiles('ALUDONATION'));
        $this->assertFalse(hasAddonBootstrapFiles('ALUCOMMITTEE'));
        $this->assertFalse(hasAddonBootstrapFiles('UNKNOWN_ADDON'));
    }

    public function test_route_list_builds_when_optional_addon_bootstrap_files_are_missing(): void
    {
        $this->artisan('route:list')->assertExitCode(0);
    }

    public function test_optional_addon_routes_are_not_registered_without_bootstrap_files(): void
    {
        $this->assertTrue(Route::has('payment.verify'));
        $this->assertFalse(Route::has('donation-payment.verify'));
        $this->assertFalse(Route::has('nomination_apply.verify'));
        $this->assertFalse(Route::has('subscription.payment.verify'));
        $this->assertFalse(Route::has('super_admin.setting.currencies.index'));
        $this->assertFalse(Route::has('super_admin.setting.gateway.index'));
        $this->assertFalse(Route::has('super_admin.setting.email-template'));
    }
}