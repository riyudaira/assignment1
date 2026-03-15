<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use App\Models\Purchase;
use App\Models\Like;

class MyListTest extends TestCase
{
    use RefreshDatabase;

    /** いいねした商品だけが表示される */
    public function test_only_liked_items_are_displayed()
    {
        /** @var User $user */
        $user = User::factory()->create();
        $this->actingAs($user);
        $likedItem = Item::factory()->create(['name' => 'いいねした腕時計']);
        $notLikedItem = Item::factory()->create(['name' => 'いいねしてないHDD']);
        Like::create([
            'user_id' => $user->id,
            'item_id' => $likedItem->id,
        ]);
        $response = $this->get('/?tab=mylist');
        $response->assertStatus(200);
        $response->assertSeeText('いいねした腕時計');
        $response->assertDontSeeText('いいねしてないHDD');
    }

    /** マイリスト内でも購入済み商品は「sold」と表示される */
    public function test_purchased_items_show_sold_label_in_mylist()
    {
        /** @var User $user */
        $user = User::factory()->create();
        $this->actingAs($user);
        $item = Item::factory()->create(['name' => '購入済みHDD']);
        Like::create([
            'user_id' => $user->id,
            'item_id' => $item->id,
        ]);
        Purchase::factory()->create([
            'user_id' => $user->id,
            'item_id' => $item->id,
            'payment_method' => 'カード支払い',
        ]);
        $response = $this->get('/?tab=mylist');
        $response->assertSee('sold');
    }

    /** 未ログインユーザーのマイリストには何も表示されない */
    public function test_guest_user_sees_nothing_in_mylist()
    {
        Item::factory()->create(['name' => '腕時計']);
        $response = $this->get('/?tab=mylist');
        $response->assertDontSeeText('腕時計');
    }
}
