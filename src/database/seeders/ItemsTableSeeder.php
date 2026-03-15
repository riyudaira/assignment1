<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Item;
use App\Models\Category;
use App\Models\User;

class ItemsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user1 = User::where('email', 'test@example.com')->first();
        $user2 = User::where('email', 'test2@example.com')->first();
        $user3 = User::where('email', 'test3@example.com')->first();
        $items = [
            [
                'user_id' => $user1->id,
                'name' => '腕時計',
                'price' => 15000,
                'brand' => 'Rolax',
                'description' => 'スタイリッシュなデザインのメンズ腕時計',
                'image_path' => 'images/img/watch.jpg',
                'condition' => '良好',
            ],
            [
                'user_id' => $user1->id,
                'name' => 'HDD',
                'price' => 5000,
                'brand' => '西芝',
                'description' => '高速で信頼性の高いハードディスク',
                'image_path' => 'images/img/disk.jpg',
                'condition' => '目立った傷や汚れなし',
            ],
            [
                'user_id' => $user1->id,
                'name' => '玉ねぎ3束',
                'price' => 300,
                'brand' => null,
                'description' => '新鮮な玉ねぎ3束のセット',
                'image_path' => 'images/img/onion.jpg',
                'condition' => 'やや傷や汚れあり',
            ],
            [
                'user_id' => $user1->id,
                'name' => '革靴',
                'price' => 4000,
                'brand' => null,
                'description' => 'クラシックなデザインの革靴',
                'image_path' => 'images/img/shoose.jpg',
                'condition' => '状態が悪い',
            ],
            [
                'user_id' => $user1->id,
                'name' => 'ノートPC',
                'price' => 45000,
                'brand' => null,
                'description' => '高性能なノートパソコン',
                'image_path' => 'images/img/laptop.jpg',
                'condition' => '良好',
            ],
            [
                'user_id' => $user2->id,
                'name' => 'マイク',
                'price' => 8000,
                'brand' => null,
                'description' => '高音質のレコーディング用マイク',
                'image_path' => 'images/img/mike.jpg',
                'condition' => '目立った傷や汚れなし',
            ],
            [
                'user_id' => $user2->id,
                'name' => 'ショルダーバッグ',
                'price' => 3500,
                'brand' => null,
                'description' => 'おしゃれなショルダーバッグ',
                'image_path' => 'images/img/bag.jpg',
                'condition' => 'やや傷や汚れあり',
            ],
            [
                'user_id' => $user2->id,
                'name' => 'タンブラー',
                'price' => 500,
                'brand' => null,
                'description' => '使いやすいタンブラー',
                'image_path' => 'images/img/Tumbler.jpg',
                'condition' => '状態が悪い',
            ],
            [
                'user_id' => $user2->id,
                'name' => 'コーヒーミル',
                'price' => 4000,
                'brand' => 'Starbacks',
                'description' => '手動のコーヒーミル',
                'image_path' => 'images/img/coffee.jpg',
                'condition' => '良好',
            ],
            [
                'user_id' => $user2->id,
                'name' => 'メイクセット',
                'price' => 2500,
                'brand' => null,
                'description' => '便利なメイクアップセット',
                'image_path' => 'images/img/makeup.jpg',
                'condition' => '目立った傷や汚れなし',
            ],
        ];
        foreach ($items as $itemData) {
            if ($itemData['name'] === '腕時計') {
                $itemData['user_id'] = $user1->id;
                $itemData['buyer_id'] = $user2->id;
                $itemData['status'] = 'shipping';
            }
            $item = Item::create($itemData);
            $categoryIds = Category::inRandomOrder()->take(rand(0, 3))->pluck('id');
            $item->categories()->attach($categoryIds);
        }
    }
}
