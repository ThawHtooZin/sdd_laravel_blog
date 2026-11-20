<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use InvalidArgumentException;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $email = 'admin@gmail.com';
        $password = 'admin1234';

        if (! is_string($email) || filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
            throw new InvalidArgumentException('Set ADMIN_EMAIL to a valid email address before seeding.');
        }

        if (! is_string($password) || Str::length($password) < 8) {
            throw new InvalidArgumentException('Set ADMIN_PASSWORD to a password with at least 8 characters before seeding.');
        }

        User::query()->updateOrCreate(
            ['email' => $email],
            [
                'name' => 'Administrator',
                'password' => $password,
            ]
        );
    }
}
