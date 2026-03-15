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

        /** @var User $user */
        $user = User::factory()->create();
        $this->actingAs($user);
        $category = Category::create(['name' => 'ファッション']);
        $file = UploadedFile::fake()->create('test.png', 100, 'image/png');
        $response = $this->post(route('item.store'), [
            'name' => 'テスト商品',
            'brand' => 'テストブランド',
            'description' => 'テスト商品の説明',
            'price' => 2000,
            'condition' => '良好',
            'categories' => [$category->id],
            'item_image' => $file,
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
        $path = str_replace('storage/', '', $item->image_path);
        $this->assertTrue(Storage::disk('public')->exists($path));
    }
}
