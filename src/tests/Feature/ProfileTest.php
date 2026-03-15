<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use App\Models\Purchase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    /** 出品した商品一覧が表示される */
    public function test_profile_page_shows_listed_items()
    {
        /** @var User $user */
        $user = User::factory()->create(['name' => 'テストユーザー']);
        $this->actingAs($user);
        Item::factory()->create([
            'user_id' => $user->id,
            'name' => '自作の腕時計'
        ]);
        $response = $this->get('/mypage?tab=listed');
        $response->assertStatus(200);
        $response->assertSee('テストユーザー');
        $response->assertSee('自作の腕時計');
    }

    /** 購入した商品一覧が表示される */
    public function test_profile_page_shows_purchased_items()
    {
        /** @var User $user */
        $user = User::factory()->create();
        $this->actingAs($user);
        $item = Item::factory()->create(['name' => '購入したHDD']);
        Purchase::factory()->create([
            'user_id' => $user->id,
            'item_id' => $item->id,
        ]);
        $response = $this->get('/mypage?tab=purchased');
        $response->assertStatus(200);
        $response->assertSee('購入したHDD');
    }

    /** プロフィール編集画面に既存のユーザー情報が表示されている */
    public function test_profile_edit_page_shows_existing_user_information()
    {
        /** @var User $user */
        $user = User::factory()->create([
            'name' => '編集前名前',
            'post_code' => '111-2222',
            'address' => '東京都渋谷区',
            'build' => 'テックビル',
        ]);
        $this->actingAs($user);
        $response = $this->get(route('user.profile.edit'));
        $response->assertStatus(200);
        $content = $response->getContent();
        $this->assertStringContainsString('value="編集前名前"', $content);
        $this->assertStringContainsString('value="111-2222"', $content);
        $this->assertStringContainsString('value="東京都渋谷区"', $content);
        $this->assertStringContainsString('value="テックビル"', $content);
        if ($user->profile_image) {
            $this->assertStringContainsString($user->profile_image, $content);
        } else {
            $this->assertStringContainsString('images/img/logo/noImage.svg', $content);
        }
    }
}
