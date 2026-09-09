<?php

namespace Tests\Feature;

use App\Events\ThemeUpdated;
use App\Livewire\Admin\Settings;
use App\Models\AppSetting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Livewire\Livewire;
use Tests\TestCase;

class RbacAndSecurityTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_pages_are_accessible_without_login()
    {
        $this->get('/kiosk')->assertStatus(200);
        $this->get('/tv')->assertStatus(200);
        $this->get('/login')->assertStatus(200);
    }

    public function test_operator_cannot_access_super_admin_settings()
    {
        $operator = User::create([
            'name' => 'Operator User',
            'email' => 'op@test.local',
            'password' => bcrypt('password'),
            'role' => 'operator',
        ]);

        $this->actingAs($operator)
            ->get('/admin/settings')
            ->assertStatus(403);
    }

    public function test_super_admin_can_update_theme_settings()
    {
        Event::fake([ThemeUpdated::class]);

        $superAdmin = User::create([
            'name' => 'Super Admin User',
            'email' => 'super@test.local',
            'password' => bcrypt('password'),
            'role' => 'super_admin',
        ]);

        Livewire::actingAs($superAdmin)
            ->test(Settings::class)
            ->set('appName', 'Kantor Pelayanan Modern')
            ->set('primaryColor', '#1e40af')
            ->set('secondaryColor', '#0d9488')
            ->call('save');

        $this->assertEquals('#1e40af', AppSetting::getValue('primary_color'));
        $this->assertEquals('#0d9488', AppSetting::getValue('secondary_color'));
        $this->assertEquals('Kantor Pelayanan Modern', AppSetting::getValue('app_name'));

        Event::assertDispatched(ThemeUpdated::class);
    }
}
