<?php

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Password;
use Tests\TestCase;
use App\Models\User;

class ResetPasswordTest extends TestCase
{
    use RefreshDatabase;

    public function test_password_reset_with_valid_data()
    {
        $user = User::factory()->create();
        
        $response = $this->post('/reset-password', [
            'token' => Password::createToken($user),
            'user_email' => $user->email,
            'password' => 'new-password',
            'password_confirmation' => 'new-password',
        ]);

        $response->assertRedirect('/login');
        $this->assertTrue(Auth::attempt([
            'email' => $user->email,
            'password' => 'new-password'
        ]));
    }
} 