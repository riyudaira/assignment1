<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use App\Models\Purchase;
use Database\Seeders\ItemsTableSeeder;
use Database\Seeders\CategoriesTableSeeder;

class PurchaseTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_purchase_requires_fields()
    {
        $users = User::factory()->count(2)->create();
        $user = $users->first();
        $this->actingAs($user);

        $this->seed(CategoriesTableSeeder::class);
        $this->seed(ItemsTableSeeder::class);

        $item = Item::where('name', '腕時計')->first();

        $response = $this->post('/purchase/' . $item->id, [
            // 空
        ]);

        $response->assertSessionHasErrors(['payment_method', 'delivery_address']);
    }
    public function test_successful_purchase_marks_item_as_sold()
    {
        $users = User::factory()->count(2)->create();
        $user = $users->first();
        $this->actingAs($user);

        $this->seed(CategoriesTableSeeder::class);
        $this->seed(ItemsTableSeeder::class);

        $item = Item::where('name', 'HDD')->first();

        $response = $this->post('/purchase/' . $item->id, [
            'payment_method' => 'カード払い',
            'delivery_address' => '東京都葛飾区テスト町1-1',
        ]);

        $response->assertRedirect();
        $response = $this->get('/purchase/' . $item->id . '/success');
        $this->assertDatabaseHas('purchases', [
            'user_id' => $user->id,
            'item_id' => $item->id,
        ]);

        $item->refresh();
        $this->assertTrue($item->isSold());
    }
    public function test_user_cannot_purchase_own_item()
    {
        $users = User::factory()->count(2)->create();
        $user = $users->first();
        $this->actingAs($user);

        $this->seed(CategoriesTableSeeder::class);
        $this->seed(ItemsTableSeeder::class);

        $ownItem = Item::where('user_id', $user->id)->first();

        $this->assertNotNull($ownItem, 'Seederで自分の商品が作成されませんでした');

        $response = $this->post('/purchase/' . $ownItem->id, [
            'payment_method' => 'カード払い',
            'delivery_address' => '東京都葛飾区テスト町1-1',
        ]);


        $response->assertSessionHasErrors();

        $this->assertDatabaseMissing('purchases', [
            'item_id' => $ownItem->id,
            'user_id' => $user->id,
        ]);
    }
    public function test_purchased_item_shows_sold_in_item_list()
    {
        $users = User::factory()->count(2)->create();
        $user = $users->first();
        $this->actingAs($user);

        $this->seed(CategoriesTableSeeder::class);
        $this->seed(ItemsTableSeeder::class);

        $item = Item::where('name', 'HDD')->first();
        $this->get('/purchase/' . $item->id . '/success');
        $response = $this->get('/');

        $response->assertSee('sold');
    }
    public function test_purchased_item_is_added_to_profile_purchase_list()
    {
        $users = User::factory()->count(2)->create();
        $user = $users->first();
        $this->actingAs($user);
        $this->seed(CategoriesTableSeeder::class);
        $this->seed(ItemsTableSeeder::class);

        $item = Item::where('name', 'HDD')->first();
        $this->get('/purchase/' . $item->id . '/success');
        $response = $this->get('/mypage?tab=purchased');
        $response->assertSee($item->name);
    }
    public function test_payment_method_selection_is_reflected()
    {
        $users = User::factory()->count(2)->create();
        $user = $users->first();
        $this->actingAs($user);

        $this->seed(CategoriesTableSeeder::class);
        $this->seed(ItemsTableSeeder::class);

        $item = Item::where('name', 'HDD')->first();

        $response = $this->post('/purchase/' . $item->id, [
            'payment_method' => 'card',
            'delivery_address' => '東京都葛飾区テスト町1-1',
        ]);

        $this->get('/purchase/' . $item->id . '/success');

        $response = $this->get('/mypage?tab=purchased');

        $response->assertSee('card');
    }
    public function test_changed_address_is_reflected_in_purchase_screen()
    {
        $users = User::factory()->count(2)->create();
        $user = $users->first();
        $this->actingAs($user);

        $this->seed(CategoriesTableSeeder::class);
        $this->seed(ItemsTableSeeder::class);

        $item = Item::where('name', 'HDD')->first();

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

    public function test_purchased_item_has_changed_address()
    {
        $users = User::factory()->count(2)->create();
        $user = $users->first();
        $this->actingAs($user);

        $this->seed(CategoriesTableSeeder::class);
        $this->seed(ItemsTableSeeder::class);

        $item = Item::where('name', 'HDD')->first();

        $this->post('/purchase/' . $item->id, [
            'payment_method'   => 'カード支払い',
            'post_code'        => '124-0001',
            'address'          => '東京都葛飾区テスト町2-2',
            'build'            => 'テストビル301',
        ]);

        $this->get('/purchase/' . $item->id . '/success');

        $this->assertDatabaseHas('purchases', [
            'user_id' => $user->id,
            'item_id' => $item->id,
        ]);
    }
}
