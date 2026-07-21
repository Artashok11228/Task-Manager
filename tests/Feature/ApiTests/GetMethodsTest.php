<?php


use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

uses(RefreshDatabase::class);

beforeEach(function () {

    $this->user =
        \App\Models\User::factory()->create([
            'email' => 'test@gmail.com',
            'password' => Hash::make('testword')
        ]);
    sanctum::actingAs($this->user, ['*']);
});

test('get all tasks', function () {
    $response = $this->get('/api/tasks');
    $response->assertStatus(200);
});

test('get all tasks by id', function () {

    $id = random_int(1, 100);
    $response = $this->get('/api/tasks/{$id}');
    $response->assertStatus(200);

});



