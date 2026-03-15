<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    /** メールアドレスが未入力の場合はエラー */
    public function test_email_is_required()
    {
        $response = $this->post('/login', [
            'password' => 'password123',
        ]);
        $response->assertSessionHasErrors(['email']);
    }

    /** パスワードが未入力の場合はエラー */
    public function test_password_is_required()
    {
        $response = $this->post('/login', [
            'email' => 'test@example.com',
        ]);
        $response->assertSessionHasErrors(['password']);
    }

    /** 間違った認証情報ではログインできない */
    public function test_invalid_credentials()
    {
        $user = User::factory()->create([
            'password' => bcrypt('password123'),
        ]);
        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'wrongpassword',
        ]);
        $this->assertGuest();
        $response->assertSessionHasErrors();
    }

    /** 正しい情報でログインに成功し、トップページへリダイレクトされる */
    public function test_successful_login()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password123'),
        ]);
        $response = $this->post('/login', [
            'email' => 'test@example.com',
            'password' => 'password123',
        ]);
        $response->assertRedirect('/');
        $this->assertAuthenticatedAs($user);
    }
}
