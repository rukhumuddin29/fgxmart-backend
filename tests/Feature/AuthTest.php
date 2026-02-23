<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Mail\WelcomeEmail;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    Mail::fake();
});

it('can register a new user', function () {
    $response = $this->postJson('/api/v1/register', [
        'name' => 'John Doe',
        'email' => 'john@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $response->assertStatus(200)
        ->assertJsonStructure([
        'access_token',
        'token_type',
        'user' => ['id', 'name', 'email']
    ]);

    $this->assertDatabaseHas('users', [
        'email' => 'john@example.com',
        'name' => 'John Doe',
    ]);

    Mail::assertSent(WelcomeEmail::class , function ($mail) {
            return $mail->hasTo('john@example.com');
        }
        );
    });

it('cannot register with existing email', function () {
    User::factory()->create(['email' => 'john@example.com']);

    $response = $this->postJson('/api/v1/register', [
        'name' => 'Jane Doe',
        'email' => 'john@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['email']);
});

it('can login with valid credentials', function () {
    $user = User::factory()->create([
        'email' => 'john@example.com',
        'password' => Hash::make('password'),
    ]);

    $response = $this->postJson('/api/v1/login', [
        'email' => 'john@example.com',
        'password' => 'password',
    ]);

    $response->assertStatus(200)
        ->assertJsonStructure(['access_token', 'token_type', 'user']);
});

it('cannot login with invalid credentials', function () {
    $user = User::factory()->create([
        'email' => 'john@example.com',
        'password' => Hash::make('password'),
    ]);

    $response = $this->postJson('/api/v1/login', [
        'email' => 'john@example.com',
        'password' => 'wrong-password',
    ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['email']);
});

it('throttles login attempts', function () {
    $email = 'john@example.com';

    for ($i = 0; $i < 5; $i++) {
        $this->postJson('/api/v1/login', [
            'email' => $email,
            'password' => 'wrong-password',
        ]);
    }

    $response = $this->postJson('/api/v1/login', [
        'email' => $email,
        'password' => 'wrong-password',
    ]);

    $response->assertStatus(429);
});
