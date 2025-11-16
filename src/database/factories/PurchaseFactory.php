<?php

namespace Database\Factories;

use App\Models\Purchase;
use App\Models\User;
use App\Models\Item;
use Illuminate\Database\Eloquent\Factories\Factory;

class PurchaseFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    protected $model = Purchase::class;

    public function definition(): array
    {
        $paymentMethods = ['コンビニ払い', 'カード支払い'];

        return [
            'user_id' => User::inRandomOrder()->first()->id,
            'item_id' => Item::inRandomOrder()->first()->id,
            'payment_method' => $this->faker->randomElement($paymentMethods),
            'post_code' => $this->faker->regexify('[0-9]{3}-[0-9]{4}'),
            'address' => $this->faker->prefecture() . $this->faker->city() . $this->faker->streetAddress(),
            'build' => $this->faker->optional()->secondaryAddress(),
            'purchased_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
