<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use App\Models\Category;
use Database\Seeders\CategoriesTableSeeder;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class ItemListingTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_item_listing_saves_information_correctly()
    {
        Storage::fake('public');
        $users = User::factory()->count(2)->create();
        $user = $users->first();
        $this->actingAs($user);
        $this->seed(CategoriesTableSeeder::class);
        $category = Category::where('name', 'ファッション')->first();

        $response = $this->post(route('item.store'), [
            'name' => 'テスト商品',
            'brand' => 'テストブランド',
            'description' => 'テスト商品の説明',
            'price' => 2000,
            'condition' => '良好',
            'categories' => [$category->id],
            'item_image' => UploadedFile::fake()->create('test.png', 100, 'image/png'),
        ]);

        $response->assertRedirect(route('user.profile'));

        $this->assertDatabaseHas('items', [
            'name' => 'テスト商品',
            'brand' => 'テストブランド',
            'description' => 'テスト商品の説明',
            'price' => 2000,
            'condition' => '良好',
            'user_id' => $user->id,
        ]);

        $item = Item::where('name', 'テスト商品')->first();
        $this->assertTrue(Storage::disk('public')->exists(str_replace('/storage/', '', $item->image_path)));
    }
}
