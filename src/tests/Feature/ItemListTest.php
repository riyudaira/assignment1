<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use App\Models\Purchase;

class ItemListTest extends TestCase
{
    use RefreshDatabase;

    /** 全商品が表示される */
    public function test_all_items_are_displayed()
    {
        Item::factory()->create(['name' => '腕時計']);
        Item::factory()->create(['name' => 'HDD']);
        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertSee('腕時計');
        $response->assertSee('HDD');
    }

    /** 購入済み商品は「sold」と表示される */
    public function test_purchased_items_show_sold_label()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create(['name' => '完売商品']);
        Purchase::factory()->create([
            'user_id' => $user->id,
            'item_id' => $item->id,
            'payment_method' => 'カード支払い',
        ]);
        $response = $this->get('/');
        $response->assertSee('sold');
    }

    /** 自分の出品物も表示される（現在のアプリの仕様に合わせる） */
    public function test_own_items_are_displayed()
    {
        /** @var User $user */
        $user = User::factory()->create();
        Item::factory()->create([
            'user_id' => $user->id,
            'name' => '私の出品物'
        ]);
        $this->actingAs($user);
        $response = $this->get('/');
        $response->assertSee('私の出品物');
    }
}
