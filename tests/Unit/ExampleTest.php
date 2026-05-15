<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class ExampleTest extends TestCase
{
    public function test_addon_bootstrap_file_map_is_stable_for_known_addons(): void
    {
        $this->assertSame([
            'config/Addon/ALUSAAS.php',
            'routes/addon/saas/frontend.php',
            'routes/addon/saas/super_admin.php',
        ], addonBootstrapFiles('ALUSAAS'));

        $this->assertSame([
            'config/Addon/ALUDONATION.php',
            'routes/addon/donation/admin.php',
            'routes/addon/donation/frontend.php',
        ], addonBootstrapFiles('ALUDONATION'));

        $this->assertSame([
            'config/Addon/ALUCOMMITTEE.php',
            'routes/addon/committee/admin.php',
            'routes/addon/committee/alumni.php',
            'routes/addon/committee/frontend.php',
        ], addonBootstrapFiles('ALUCOMMITTEE'));
    }

    public function test_unknown_addon_codes_have_no_bootstrap_files(): void
    {
        $this->assertSame([], addonBootstrapFiles('UNKNOWN_ADDON'));
    }
}
