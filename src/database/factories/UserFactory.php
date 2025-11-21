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
        // 日本語対応の名前・住所生成
        $lastName = $this->faker->lastKanaName();
        $firstName = $this->faker->firstKanaName();
        $fullName = mb_substr($lastName . ' ' . $firstName, 0, 20);

        // ハイフンありの郵便番号（例：123-4567）
        $postCode = $this->faker->regexify('[0-9]{3}-[0-9]{4}');

        // 拡張子が.jpegまたは.pngの画像パス
        $imageExtension = $this->faker->randomElement(['jpeg', 'png']);
        $profileImage = 'profile_' . $this->faker->unique()->numerify('###') . '.' . $imageExtension;

        return [
            'name' => $fullName,
            'email' => $this->faker->unique()->safeEmail(),
            'password' => Hash::make('password123'),
            'post_code' => $postCode,
            'address' => $this->faker->prefecture() . $this->faker->city() . $this->faker->streetAddress(),
            'build' => $this->faker->optional()->secondaryAddress(),
            'email_verified_at' => now(),
            'profile_image' => $profileImage,
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
