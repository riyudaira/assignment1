<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

class UserFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $lastName = $this->faker->lastKanaName();
        $firstName = $this->faker->firstKanaName();
        $fullName = mb_substr($lastName . ' ' . $firstName, 0, 20);
        $postCode = $this->faker->regexify('[0-9]{3}-[0-9]{4}');
        $imageExtension = $this->faker->randomElement(['jpeg', 'png']);

        return [
            'name' => $fullName,
            'email' => $this->faker->unique()->safeEmail(),
            'password' => Hash::make('password123'),
            'post_code' => $postCode,
            'address' => $this->faker->prefecture() . $this->faker->city() . $this->faker->streetAddress(),
            'build' => $this->faker->optional()->secondaryAddress(),
            'email_verified_at' => now(),
            'profile_image' => null,
            'evaluation' => $this->faker->randomFloat(1, 1, 5),
            'evaluation_count' => $this->faker->numberBetween(0, 100),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function unverified()
    {
        return $this->state(function (array $attributes) {
            return [
                'email_verified_at' => null,
            ];
        });
    }
}
