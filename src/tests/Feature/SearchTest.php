<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Like;
use Database\Seeders\ItemsTableSeeder;
use Database\Seeders\CategoriesTableSeeder;

class SearchTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     *
     * @return void
     */

    public function test_search_items_by_name()
    {
        \App\Models\User::factory()->create();

        $this->seed(\Database\Seeders\CategoriesTableSeeder::class);
        $this->seed(\Database\Seeders\ItemsTableSeeder::class);
        $response = $this->get('/?keyword=腕');
        $response->assertSeeText('腕時計');
        $response->assertDontSeeText('HDD');
    }

    public function test_search_keyword_is_retained_in_mylist()
    {
        $users = User::factory()->count(2)->create();
        $user = $users->first();
        $this->actingAs($user);

        $this->seed(CategoriesTableSeeder::class);
        $this->seed(ItemsTableSeeder::class);

        $item = \App\Models\Item::where('name', 'HDD')->first();
        Like::create([
            'user_id' => $user->id,
            'item_id' => $item->id,
        ]);

        $response = $this->get('/?keyword=HDD');
        $response->assertSeeText('HDD');

        $response = $this->get('/?tab=mylist&keyword=HDD');
        $response->assertSeeText('HDD');
        $response->assertDontSeeText('腕時計');
    }
}
