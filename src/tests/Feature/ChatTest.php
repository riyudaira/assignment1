<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use App\Models\Chat;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;
use App\Mail\TransactionCompleted;

class ChatTest extends TestCase
{
    use RefreshDatabase;

    /** メッセージと画像が正しく保存されるか */
    public function test_message_and_image_are_stored_correctly()
    {
        Storage::fake('public');
        /** @var User $user */
        $user = User::factory()->create();
        $item = Item::factory()->create(['user_id' => $user->id]);
        $this->actingAs($user);
        $image = UploadedFile::fake()->create('test_chat.jpg', 100);
        $response = $this->post(route('chat.store', ['item' => $item->id]), [
            'message' => '画像付きメッセージです',
            'image' => $image,
        ]);
        $response->assertStatus(302);
        $this->assertDatabaseHas('chats', [
            'item_id' => $item->id,
            'text' => '画像付きメッセージです',
        ]);
        $chat = Chat::first();
        $this->assertTrue(Storage::disk('public')->exists($chat->image_path));
    }

    /** 自分と相手以外の無関係なユーザーには見えないか */
    public function test_unrelated_user_cannot_access_chat()
    {
        /** @var User $seller */
        $seller = User::factory()->create();
        /** @var User $buyer */
        $buyer = User::factory()->create();
        /** @var User $unrelated */
        $unrelated = User::factory()->create();
        $item = Item::factory()->create([
            'user_id' => $seller->id,
            'buyer_id' => $buyer->id,
            'status' => 'shipping'
        ]);
        $this->actingAs($unrelated);
        $response = $this->get(route('chat.show', ['item' => $item->id]));
        $response->assertStatus(403);
    }

    /** 取引完了処理ができるか（購入者のみ） */
    public function test_buyer_can_complete_transaction()
    {
        Mail::fake();
        /** @var User $seller */
        $seller = User::factory()->create();
        /** @var User $buyer */
        $buyer = User::factory()->create();
        $item = Item::factory()->create([
            'user_id' => $seller->id,
            'buyer_id' => $buyer->id,
            'status' => 'shipping'
        ]);
        $this->actingAs($buyer);
        $response = $this->post(route('purchase.complete', ['item' => $item->id]));
        $response->assertStatus(302);
        $this->assertEquals('sold', $item->fresh()->status);
        Mail::assertSent(TransactionCompleted::class);
    }

    /** 評価が適切に更新されるか */
    public function test_user_evaluation_is_calculated_correctly()
    {
        /** @var User $seller */
        $seller = User::factory()->create(['evaluation' => 4.0, 'evaluation_count' => 1]);
        /** @var User $buyer */
        $buyer = User::factory()->create();
        $item = Item::factory()->create([
            'user_id' => $seller->id,
            'buyer_id' => $buyer->id,
            'status' => 'sold'
        ]);
        $this->actingAs($buyer);
        $response = $this->post(route('items.review.store', ['item' => $item->id]), [
            'rating' => 5,
        ]);
        $response->assertStatus(302);
        $seller->refresh();
        $this->assertEquals(4.5, $seller->evaluation);
        $this->assertEquals(2, $seller->evaluation_count);
    }
}
