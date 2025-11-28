<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use App\Models\Category;
use App\Models\Comment;
use Database\Seeders\ItemsTableSeeder;
use Database\Seeders\CategoriesTableSeeder;

class ItemDetailTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_item_detail_displays_all_information()
    {
        $users = User::factory()->count(2)->create();
        $user = $users->first();
        $this->actingAs($user);

        $this->seed(CategoriesTableSeeder::class);
        $this->seed(ItemsTableSeeder::class);

        $item = Item::where('name', '腕時計')->first();
        Comment::create([
            'user_id' => $user->id,
            'item_id' => $item->id,
            'content' => 'かっこいいですね！',
        ]);

        $response = $this->get('/item/' . $item->id);

        $response->assertSeeText('腕時計');
        $response->assertSeeText('Rolax');
        $response->assertSeeText('¥15,000（税込）');
        $response->assertSeeText('スタイリッシュなデザイン');
        $response->assertSeeText('良好');
        $response->assertSeeText('かっこいいですね！');
    }

    public function test_item_detail_displays_multiple_categories()
    {
        $users = User::factory()->count(2)->create();
        $user = $users->first();
        $this->actingAs($user);

        $this->seed(CategoriesTableSeeder::class);
        $this->seed(ItemsTableSeeder::class);

        $item = Item::where('name', '腕時計')->first();

        $categories = Category::take(2)->pluck('id');
        $item->categories()->sync($categories);

        $response = $this->get('/item/' . $item->id);

        foreach (Category::find($categories) as $category) {
            $response->assertSeeText($category->name);
        }
    }
}
