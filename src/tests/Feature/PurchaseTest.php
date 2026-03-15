<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use App\Models\Purchase;

class PurchaseTest extends TestCase
{
    use RefreshDatabase;

    /** 購入には支払い方法と配送先が必須 */
    public function test_purchase_requires_fields()
    {
        /** @var User $user */
        $user = User::factory()->create();
        $item = Item::factory()->create();
        $this->actingAs($user);
        $response = $this->post('/purchase/' . $item->id, []);
        $response->assertSessionHasErrors(['payment_method', 'delivery_address']);
    }

    /** 購入に成功し、商品が売却済みになる */
    public function test_successful_purchase_marks_item_as_sold()
    {
        /** @var User $user */
        $user = User::factory()->create();
        $item = Item::factory()->create(['name' => 'テスト商品']);
        $this->actingAs($user);
        $addressData = [
            'post_code' => '123-4567',
            'address' => '東京都葛飾区テスト町1-1',
            'build' => 'テストビル',
        ];
        session(['purchase_address_' . $item->id => $addressData]);
        $response = $this->get('/purchase/' . $item->id . '/success');
        $response->assertRedirect(route('user.profile'));
        $this->assertDatabaseHas('purchases', [
            'user_id' => $user->id,
            'item_id' => $item->id,
            'address' => '東京都葛飾区テスト町1-1',
        ]);
    }

    /** 自分の商品は購入できない */
    public function test_user_cannot_purchase_own_item()
    {
        /** @var User $user */
        $user = User::factory()->create();
        $ownItem = Item::factory()->create(['user_id' => $user->id]);
        $this->actingAs($user);
        $response = $this->post('/purchase/' . $ownItem->id, [
            'payment_method' => 'カード払い',
            'delivery_address' => '東京都葛飾区テスト町1-1',
        ]);
        $response->assertSessionHasErrors();
        $this->assertDatabaseMissing('purchases', ['item_id' => $ownItem->id]);
    }

    /** 変更した配送先が購入画面に反映される */
    public function test_changed_address_is_reflected_in_purchase_screen()
    {
        /** @var User $user */
        $user = User::factory()->create();
        $item = Item::factory()->create();
        $this->actingAs($user);
        session([
            'purchase_address_' . $item->id => [
                'address' => '東京都葛飾区新しい住所',
                'post_code' => '987-6543',
                'build' => '新ビル202',
            ]
        ]);
        $response = $this->get('/purchase/' . $item->id);
        $response->assertSee('東京都葛飾区新しい住所');
        $response->assertSee('987-6543');
        $response->assertSee('新ビル202');
    }

    /** 購入後にマイページの購入一覧に表示される */
    public function test_purchased_item_is_added_to_profile_purchase_list()
    {
        /** @var User $user */
        $user = User::factory()->create();
        $item = Item::factory()->create(['name' => '履歴に載る商品']);
        $this->actingAs($user);
        Purchase::factory()->create([
            'user_id' => $user->id,
            'item_id' => $item->id,
        ]);
        $response = $this->get('/mypage?tab=purchased');
        $response->assertSee('履歴に載る商品');
    }
}
