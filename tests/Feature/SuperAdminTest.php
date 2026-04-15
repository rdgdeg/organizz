<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SuperAdminTest extends TestCase
{
    use RefreshDatabase;

    public function test_non_super_admin_cannot_access_administration(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $this->get(route('administration.utilisateurs.index'))->assertForbidden();
    }

    public function test_super_admin_can_access_administration_users(): void
    {
        $admin = User::factory()->create();
        $admin->forceFill(['is_super_admin' => true])->save();

        $this->actingAs($admin);

        $this->get(route('administration.utilisateurs.index'))->assertOk();
    }
}
