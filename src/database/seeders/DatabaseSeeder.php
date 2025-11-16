<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            UsersTableSeeder::class,
            CategoriesTableSeeder::class, // ← 先にカテゴリを作成
            ItemsTableSeeder::class,
            PurchasesTableSeeder::class,
            LikesTableSeeder::class,
            CommentsTableSeeder::class,
        ]);
    }
}
