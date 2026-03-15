<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'user_id' => \App\Models\User::factory(),
            'name' => 'テスト商品',
            'price' => 1000,
            'description' => 'テスト説明文',
            'image_path' => 'images/img/watch.jpg',
            'condition' => '良好',
            'status' => 'selling',
        ];
    }
}
