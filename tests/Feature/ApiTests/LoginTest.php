<?php

use Illuminate\Foundation\Testing\RefreshDatabase;


uses(RefreshDatabase::class);
//to refresh databse for every test and the mock data and the factories in tests wouldnt be messed up

test('test login without headers', function () {
    $response = $this->post('/api/login');

    $response->assertJsonValidationErrors([
        'email',
        'password'
    ]);
});

test('test login with wrong data', function () {
    $response = $this->postJson('/api/login', [
        'email' => 'wrongemail@gmail.com',
        'password' => 'wrongpassword'
    ]);

    $response->assertStatus(401);
});


test('test login with correct data', function () {

    App\Models\User::factory()->create([
        'email' => 'test@gmail.com',
        'password' => Hash::make('1234'),
    ]);

    $response = $this->postJson('/api/login', [
        'email' => 'test@gmail.com',
        'password' => '1234'
    ]);
    $response
        ->assertOk()
        ->assertJsonStructure(['token'])
        ->assertStatus(200);
});










