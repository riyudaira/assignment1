<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Purchase;
use App\Models\Item;
use App\Models\User;

class PurchasesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $item = Item::where('name', '腕時計')->first();
        $user2 = User::where('email', 'test2@example.com')->first();
        if ($item && $user2) {
            Purchase::create([
                'item_id' => $item->id,
                'user_id' => $user2->id,
                'payment_method' => 'カード支払い',
                'post_code' => $user2->post_code ?? '000-0000',
                'address' => $user2->address ?? '未設定',
                'build' => $user2->build,
                'purchased_at' => now(),
            ]);
        }
    }
}
