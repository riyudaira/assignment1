<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use Database\Seeders\UsersTableSeeder;
use Database\Seeders\CategoriesTableSeeder;
use Database\Seeders\ItemsTableSeeder;

class ProfileTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_profile_page_shows_listed_items()
    {
        $this->seed(UsersTableSeeder::class);
        $this->seed(CategoriesTableSeeder::class);
        $this->seed(ItemsTableSeeder::class);

        $item = Item::where('name', '腕時計')->first();
        $user = $item->user;

        $this->actingAs($user);

        $response = $this->get('/mypage?tab=listed');

        $response->assertSee($user->name);
        $response->assertSee('腕時計');
    }

    public function test_profile_page_shows_purchased_items()
    {
        $this->seed(UsersTableSeeder::class);
        $this->seed(CategoriesTableSeeder::class);
        $this->seed(ItemsTableSeeder::class);

        $user = User::first();
        $this->actingAs($user);

        $item = Item::where('name', 'HDD')->first();

        $user->purchases()->create([
            'item_id' => $item->id,
            'payment_method' => 'カード支払い',
            'post_code' => '123-4567',
            'address' => '東京都葛飾区テスト町',
            'build' => 'テストビル101',
            'purchased_at' => now(),
        ]);

        $response = $this->get('/mypage?tab=purchased');
        $response->assertSee('HDD');
    }
    public function test_profile_edit_page_shows_existing_user_information()
    {
        $user = User::factory()->count(1)->create()->first();
        $this->actingAs($user);

        $response = $this->get(route('user.profile.edit'));

        $content = $response->getContent();
        $this->assertStringContainsString('value="' . $user->name . '"', $content);
        if ($user->profile_image) {
            $this->assertStringContainsString($user->profile_image, $content);
        } else {
            $this->assertStringContainsString('noImage.svg', $content);
        }
        $this->assertStringContainsString('value="' . $user->post_code . '"', $content);
        $this->assertStringContainsString('value="' . $user->address . '"', $content);
        $this->assertStringContainsString('value="' . $user->build . '"', $content);
    }
}
