<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Facades\Notification;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Support\Facades\URL;

class EmailVerificationTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_registration_sends_verification_email()
    {
        Notification::fake();
        $response = $this->post('/register', [
            'name' => 'テストユーザー',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertRedirect(route('verification.notice'));
        $user = User::firstWhere('email', 'test@example.com');
        Notification::assertSentTo($user, VerifyEmail::class);
    }
    public function test_verification_notice_page_has_resend_link()
    {
        $user = User::factory()->unverified()->create();
        $this->actingAs($user);
        $response = $this->get(route('verification.notice'));

        $response->assertStatus(200);

        $response->assertSee('認証はこちらから');
        $response->assertSee(route('verification.send'));
    }
    public function test_user_can_verify_email_and_redirect_to_profile()
    {
        $user = User::factory()->unverified()->create([
            'profile_completed' => false,
        ]);
        $verificationUrl = \Illuminate\Support\Facades\URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => $user->id, 'hash' => sha1($user->email)]
        );
        $response = $this->actingAs($user)->get($verificationUrl);
        $response->assertRedirect(route('verified.redirect'));
        $this->assertNotNull($user->fresh()->email_verified_at);
    }
}
