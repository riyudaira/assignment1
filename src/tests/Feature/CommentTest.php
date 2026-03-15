<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use Database\Seeders\CategoriesTableSeeder;

class CommentTest extends TestCase
{
    use RefreshDatabase;

    /**
     * ログイン済みユーザーはコメントを送信できる
     */
    public function test_authenticated_user_can_post_comment()
    {
        /** @var User $user */
        $user = User::factory()->create();
        $item = Item::factory()->create();
        $this->actingAs($user);
        $response = $this->post('/item/' . $item->id . '/comment', [
            'content' => '素敵な商品ですね！',
        ]);
        $response->assertRedirect('/item/' . $item->id);
        $this->assertDatabaseHas('comments', [
            'user_id' => $user->id,
            'item_id' => $item->id,
            'content' => '素敵な商品ですね！',
        ]);
    }

    /**
     * 未ログインユーザーはコメント送信できない
     */
    public function test_guest_user_cannot_post_comment()
    {
        $item = Item::factory()->create();
        $response = $this->post('/item/' . $item->id . '/comment', [
            'content' => 'ゲストコメント',
        ]);
        $response->assertRedirect('/login');
        $this->assertDatabaseMissing('comments', [
            'content' => 'ゲストコメント',
        ]);
    }

    /**
     * コメント未入力時はバリデーションメッセージ
     */
    public function test_comment_is_required()
    {
        /** @var User $user */
        $user = User::factory()->create();
        $item = Item::factory()->create();
        $this->actingAs($user);
        $response = $this->post('/item/' . $item->id . '/comment', [
            'content' => '',
        ]);
        $response->assertSessionHasErrors(['content']);
    }

    /**
     * コメントが255字以上の場合はバリデーションエラー
     */
    public function test_comment_must_not_exceed_255_characters()
    {
        /** @var User $user */
        $user = User::factory()->create();
        $item = Item::factory()->create();
        $this->actingAs($user);
        $longComment = str_repeat('あ', 256);
        $response = $this->post('/item/' . $item->id . '/comment', [
            'content' => $longComment,
        ]);
        $response->assertSessionHasErrors(['content']);
    }
}
