<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Chat;
use App\Models\Item;

class ChatsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $item = Item::where('name', '腕時計')->first();
        if ($item && $item->buyer_id) {
            $messages = [
                ['user_id' => $item->buyer_id, 'text' => '購入しました！よろしくお願いします。', 'is_seller' => false, 'is_read' => true],
                ['user_id' => $item->user_id, 'text' => 'ご購入ありがとうございます。明日発送しますね。', 'is_seller' => true, 'is_read' => true],
                ['user_id' => $item->buyer_id, 'text' => '承知いたしました。到着を楽しみにしています！', 'is_seller' => false, 'is_read' => true],
                ['user_id' => $item->user_id, 'text' => '本日発送が完了しました。画像も添付しておきます。', 'is_seller' => true, 'is_read' => false],
            ];
            foreach ($messages as $msg) {
                Chat::create([
                    'item_id' => $item->id,
                    'user_id' => $msg['user_id'],
                    'text' => $msg['text'],
                    'is_seller' => $msg['is_seller'],
                    'is_read'   => $msg['is_read'],
                ]);
            }
        }
    }
}
