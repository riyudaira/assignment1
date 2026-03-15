<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use App\Models\Category;
use App\Models\Comment;

class ItemDetailTest extends TestCase
{
    use RefreshDatabase;

    /** 商品詳細画面にすべての情報が表示される */
    public function test_item_detail_displays_all_information()
    {
        /** @var User $user */
        $user = User::factory()->create();
        $this->actingAs($user);
        $item = Item::factory()->create([
            'name' => '腕時計',
            'brand' => 'Rolax',
            'price' => 15000,
            'description' => 'スタイリッシュなデザインのメンズ腕時計',
            'condition' => '良好',
        ]);
        Comment::create([
            'user_id' => $user->id,
            'item_id' => $item->id,
            'content' => 'かっこいいですね！',
        ]);
        $response = $this->get('/item/' . $item->id);
        $response->assertStatus(200);
        $response->assertSeeText('腕時計');
        $response->assertSeeText('Rolax');
        $response->assertSeeText('15,000');
        $response->assertSeeText('スタイリッシュなデザイン');
        $response->assertSeeText('良好');
        $response->assertSeeText('かっこいいですね！');
    }

    /** 商品詳細画面に複数のカテゴリーが表示される */
    public function test_item_detail_displays_multiple_categories()
    {
        /** @var User $user */
        $user = User::factory()->create();
        $this->actingAs($user);
        $category1 = Category::create(['name' => 'ファッション']);
        $category2 = Category::create(['name' => 'メンズ']);
        $item = Item::factory()->create();
        $item->categories()->attach([$category1->id, $category2->id]);
        $response = $this->get('/item/' . $item->id);
        $response->assertSeeText('ファッション');
        $response->assertSeeText('メンズ');
    }
}
