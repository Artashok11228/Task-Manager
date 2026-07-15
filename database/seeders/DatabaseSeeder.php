<?php

namespace Database\Seeders;

use App\Models\Status;
use App\Models\Task;
use App\Models\User;
use Database\Factories\TaskFactory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

//        User::factory()->create([
//            'name' => 'Test User',
//            'email' => 'testg@example.com',
//        ]);

//        Task::factory()->count(10)->create(TaskFactory::class);

        Status::factory()->create([
            'name' => 'to do',
            'color' => 'white',
        ]);
        Status::factory()->create([
            'name' => 'in Progress',
            'color' => 'purple',
        ]);
        Status::factory()->create([
            'name' => 'completed',
            'color' => 'green',
        ]);

        Status::factory()->create([
            'name' => 'hold',
            'color' => 'gray',
        ]);

        User::factory()->create([
            'email'=> 'arta@gmail.com',
            'password' => '1234',
        ]);

        User::factory()->create([
            'email' => 'ilia@gmail.com',
            'password' => '1234',
        ]);




    }
}
