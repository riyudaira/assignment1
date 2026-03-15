<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use App\Models\Like;

class SearchTest extends TestCase
{
    use RefreshDatabase;

    /** 商品名で検索ができる */
    public function test_search_items_by_name()
    {
        Item::factory()->create(['name' => '限定版の腕時計']);
        Item::factory()->create(['name' => '高性能なHDD']);
        $response = $this->get('/?keyword=腕');
        $response->assertStatus(200);
        $response->assertSeeText('限定版の腕時計');
        $response->assertDontSeeText('高性能なHDD');
    }

    /** マイリスト内でも検索キーワードが保持され、絞り込みが機能する */
    public function test_search_keyword_is_retained_in_mylist()
    {
        /** @var User $user */
        $user = User::factory()->create();
        $this->actingAs($user);
        $item1 = Item::factory()->create(['name' => 'お気に入りのHDD']);
        $item2 = Item::factory()->create(['name' => '対象外の腕時計']);
        Like::create([
            'user_id' => $user->id,
            'item_id' => $item1->id,
        ]);
        $response = $this->get('/?tab=mylist&keyword=HDD');
        $response->assertStatus(200);
        $response->assertSeeText('お気に入りのHDD');
        $response->assertDontSeeText('対象外の腕時計');
    }
}
