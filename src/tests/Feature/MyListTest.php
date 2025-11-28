<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use App\Models\Purchase;
use App\Models\Like;
use Database\Seeders\ItemsTableSeeder;
use Database\Seeders\CategoriesTableSeeder;

class MyListTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     *
     * @return void
     */

    public function test_only_liked_items_are_displayed()
    {
        $users = User::factory()->count(2)->create();
        $user = $users->first();
        $this->actingAs($user);

        $this->seed(CategoriesTableSeeder::class);
        $this->seed(ItemsTableSeeder::class);

        $item1 = Item::where('name', '腕時計')->first();
        $item2 = Item::where('name', 'HDD')->first();

        Like::create([
            'user_id' => $user->id,
            'item_id' => $item1->id,
        ]);

        $response = $this->get('/?tab=mylist');
        $response->assertSeeText('腕時計');
        $response->assertDontSeeText('HDD');
    }

    public function test_purchased_items_show_sold_label_in_mylist()
    {
        $users = User::factory()->count(2)->create();
        $user = $users->first();
        $this->actingAs($user);

        $this->seed(CategoriesTableSeeder::class);
        $this->seed(ItemsTableSeeder::class);

        $item = Item::where('name', 'HDD')->first();

        Like::create([
            'user_id' => $user->id,
            'item_id' => $item->id,
        ]);

        Purchase::create([
            'user_id' => $user->id,
            'item_id' => $item->id,
            'payment_method' => 'card',
            'post_code' => '123-4567',
            'address' => '東京都葛飾区テスト町1-1',
            'purchased_at' => now(),
        ]);

        $response = $this->get('/?tab=mylist');
        $response->assertSeeText('sold');
    }

    public function test_guest_user_sees_nothing_in_mylist()
    {
        User::factory()->create();

        $this->seed(CategoriesTableSeeder::class);
        $this->seed(ItemsTableSeeder::class);

        $response = $this->get('/?tab=mylist');
        $response->assertDontSeeText('腕時計');
        $response->assertDontSeeText('HDD');
    }
}
