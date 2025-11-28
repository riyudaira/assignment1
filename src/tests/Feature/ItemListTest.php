<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use App\Models\Purchase;
use App\Models\Category;
use Database\Seeders\ItemsTableSeeder;

class ItemListTest extends TestCase
{
    use RefreshDatabase;


    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_all_items_are_displayed()
    {
        User::factory()->create();
        $this->seed(\Database\Seeders\CategoriesTableSeeder::class);
        $this->seed(\Database\Seeders\ItemsTableSeeder::class);

        $response = $this->get('/');
        $response->dump();

        $response->assertSee('腕時計', false);
        $response->assertSee('HDD', false);
    }
    public function test_purchased_items_show_sold_label()
    {
        $user = User::factory()->create();

        $this->seed(\Database\Seeders\CategoriesTableSeeder::class);
        $this->seed(\Database\Seeders\ItemsTableSeeder::class);

        $item = Item::first();

        Purchase::factory()->create([
            'user_id' => $user->id,
            'item_id' => $item->id,
        ]);

        $response = $this->get('/items');
        $response->assertSee('sold');
    }
    public function test_own_items_are_not_displayed()
    {
        $users = User::factory()->count(2)->create();
        $user = $users->first();

        $this->seed(\Database\Seeders\CategoriesTableSeeder::class);
        $this->seed(\Database\Seeders\ItemsTableSeeder::class);

        $item = Item::first();
        $item->update(['user_id' => $user->id]);

        $this->actingAs($user);

        $response = $this->get('/items');
        $response->assertDontSee($item->name);
    }
}
