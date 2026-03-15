<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::factory()->create([
            'name' => 'テストユーザー1',
            'email' => 'test@example.com',
            'password' => bcrypt('abab1234'),
            'evaluation' => 0.0,
            'evaluation_count' => 0,
        ]);
        User::factory()->create([
            'name' => 'テストユーザー2',
            'email' => 'test2@example.com',
            'password' => bcrypt('1234abab'),
            'evaluation' => 0.0,
            'evaluation_count' => 0,
        ]);
        User::factory()->create([
            'name' => 'テストユーザー3',
            'email' => 'test3@example.com',
            'password' => bcrypt('abcd4321'),
            'evaluation' => 0.0,
            'evaluation_count' => 0,
        ]);
        User::factory()->count(10)->create();
    }
}
