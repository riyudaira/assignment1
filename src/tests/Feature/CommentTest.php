<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use App\Models\Comment;
use Database\Seeders\ItemsTableSeeder;
use Database\Seeders\CategoriesTableSeeder;

class CommentTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     *
     * @return void
     */
    /** ログイン済みユーザーはコメントを送信できる */
    public function test_authenticated_user_can_post_comment()
    {
        $users = User::factory()->count(2)->create();
        $user = $users->first();
        $this->actingAs($user);

        $this->seed(CategoriesTableSeeder::class);
        $this->seed(ItemsTableSeeder::class);

        $item = Item::where('name', '腕時計')->first();

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
    /** 未ログインユーザーはコメント送信できない */
    public function test_guest_user_cannot_post_comment()
    {
        \App\Models\User::factory()->create();

        $this->seed(\Database\Seeders\CategoriesTableSeeder::class);
        $this->seed(\Database\Seeders\ItemsTableSeeder::class);

        $item = \App\Models\Item::where('name', '腕時計')->first();

        $response = $this->post('/item/' . $item->id . '/comment', [
            'content' => 'ゲストコメント',
        ]);

        $response->assertRedirect('/login');
        $this->assertDatabaseMissing('comments', [
            'content' => 'ゲストコメント',
        ]);
    }
    /** コメント未入力時はバリデーションメッセージ */
    public function test_comment_is_required()
    {
        $users = User::factory()->count(2)->create();
        $user = $users->first();
        $this->actingAs($user);

        $this->seed(CategoriesTableSeeder::class);
        $this->seed(ItemsTableSeeder::class);

        $item = Item::where('name', '腕時計')->first();

        $response = $this->post('/item/' . $item->id . '/comment', [
            'content' => '',
        ]);

        $response->assertSessionHasErrors(['content']);
    }
    /** コメントが255字以上の場合はバリデーションエラー */
    public function test_comment_must_not_exceed_255_characters()
    {
        $users = User::factory()->count(2)->create();
        $user = $users->first();
        $this->actingAs($user);

        $this->seed(CategoriesTableSeeder::class);
        $this->seed(ItemsTableSeeder::class);

        $item = Item::where('name', '腕時計')->first();

        $longComment = str_repeat('あ', 256);

        $response = $this->post('/item/' . $item->id . '/comment', [
            'content' => $longComment,
        ]);

        $response->assertSessionHasErrors(['content']);
    }
}
