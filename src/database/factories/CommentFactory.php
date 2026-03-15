<?php

namespace Database\Factories;

use App\Models\Comment;
use App\Models\User;
use App\Models\Item;
use Illuminate\Database\Eloquent\Factories\Factory;

class CommentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    protected $model = Comment::class;

    public function definition(): array
    {
        $comments = [
            'とても良い商品でした！また利用したいです。',
            '発送が早くて助かりました。',
            '写真通りの品物で満足しています。',
            '少し汚れがありましたが、許容範囲です。',
            '丁寧な対応ありがとうございました。',
            '梱包がしっかりしていて安心でした。',
            '思ったよりもサイズが小さかったです。',
            'デザインが気に入っています！',
            '説明通りの状態で届きました。',
            'コスパが良くておすすめです。',
        ];
        $item = Item::inRandomOrder()->first();
        $commenter = User::where('id', '!=', $item->user_id)
            ->inRandomOrder()
            ->first() ?? User::factory();

        return [
            'user_id' => User::inRandomOrder()->first()->id,
            'item_id' => Item::inRandomOrder()->first()->id,
            'content' => $this->faker->randomElement($comments),
        ];
    }
}
