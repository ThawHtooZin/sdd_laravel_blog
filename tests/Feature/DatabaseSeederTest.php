<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use InvalidArgumentException;
use Tests\TestCase;

class DatabaseSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_is_seeded_with_configured_email_and_hashed_password(): void
    {
        config()->set('admin.email', 'administrator@example.test');
        config()->set('admin.password', 'A-Long-Test-Password-123!');

        $this->seed(DatabaseSeeder::class);

        $admin = User::query()->where('email', 'administrator@example.test')->firstOrFail();

        $this->assertTrue(Hash::check('A-Long-Test-Password-123!', $admin->password));
    }

    public function test_seeder_rejects_a_short_admin_password(): void
    {
        config()->set('admin.email', 'administrator@example.test');
        config()->set('admin.password', 'short');

        $this->expectException(InvalidArgumentException::class);

        $this->seed(DatabaseSeeder::class);
    }

    public function test_seeder_rejects_a_missing_admin_password(): void
    {
        config()->set('admin.email', 'administrator@example.test');
        config()->set('admin.password', null);

        $this->expectException(InvalidArgumentException::class);

        $this->seed(DatabaseSeeder::class);
    }
}
