<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserSeeder extends Seeder
{
    public function run()
    {
        DB::collection('users')->delete();

        DB::collection('users')->insert([
            'name' => "Admin",
            'email' => "admin@mail.com",
            'password' => \Illuminate\Support\Facades\Hash::make('password'),
            'role' => \App\User::ROLE_ADMIN,
            'active' => true,
            'language' => config('app.locale'),
            'created_at' => (new \Illuminate\Support\Carbon())->toDateTimeString(),
            'updated_at' => (new \Illuminate\Support\Carbon())->toDateTimeString(),
        ]);
    }
}
