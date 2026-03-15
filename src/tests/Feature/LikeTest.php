<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use App\Models\Like;

class LikeTest extends TestCase
{
    use RefreshDatabase;

    /** ログインユーザーは商品にいいねができる */
    public function test_user_can_like_an_item()
    {
        /** @var User $user */
        $user = User::factory()->create();
        $item = Item::factory()->create();
        $this->actingAs($user);
        $response = $this->post('/item/' . $item->id . '/like');
        $response->assertStatus(200);
        $this->assertDatabaseHas('likes', [
            'user_id' => $user->id,
            'item_id' => $item->id,
        ]);
    }

    /** いいね済みの商品のアイコン状態が変わる */
    public function test_liked_item_icon_changes_color()
    {
        /** @var User $user */
        $user = User::factory()->create();
        $item = Item::factory()->create();
        $this->actingAs($user);
        Like::create([
            'user_id' => $user->id,
            'item_id' => $item->id,
        ]);
        $response = $this->get('/item/' . $item->id);
        $response->assertSee('liked');
    }

    /** 再度いいねを押すと解除される（トグル機能） */
    public function test_user_can_unlike_an_item()
    {
        /** @var User $user */
        $user = User::factory()->create();
        $item = Item::factory()->create();
        $this->actingAs($user);
        Like::create([
            'user_id' => $user->id,
            'item_id' => $item->id,
        ]);
        $response = $this->post('/item/' . $item->id . '/like');
        $response->assertStatus(200);
        $this->assertDatabaseMissing('likes', [
            'user_id' => $user->id,
            'item_id' => $item->id,
        ]);
    }
}
