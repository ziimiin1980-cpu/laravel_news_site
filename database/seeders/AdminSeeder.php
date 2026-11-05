<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use RuntimeException;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        $username = config('app.admin.username');
        $email = config('app.admin.email');
        $password = config('app.admin.password');

        if (! is_string($username) || $username === ''
            || ! is_string($email) || filter_var($email, FILTER_VALIDATE_EMAIL) === false
            || ! is_string($password) || strlen($password) < 16) {
            throw new RuntimeException(
                'Set ADMIN_USERNAME, a valid ADMIN_EMAIL, and an ADMIN_PASSWORD of at least 16 characters.'
            );
        }

        $adminRole = Role::firstOrCreate(['name' => 'admin']);

        $adminAttributes = [
            'name' => 'Administrator',
            'email' => $email,
            'password' => $password,
        ];

        $admin = User::where('username', $username)->first();

        if ($admin === null) {
            $admin = $adminRole->users()->create([
                ...$adminAttributes,
                'username' => $username,
            ]);
        } else {
            $admin->update($adminAttributes);
        }

        $admin->role()->associate($adminRole);
        $admin->is_blocked = false;
        $admin->save();
    }
}
