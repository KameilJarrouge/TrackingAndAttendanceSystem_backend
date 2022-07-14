<?php

namespace Database\Seeders;

use App\Models\Semester;
use App\Models\Setting;
use App\Models\Student;
use App\Models\User;
use Faker\Provider\Person;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // \App\Models\User::factory(10)->create();
        Setting::query()->create();
        $kamil =  User::query()->create(['username' => 'kamil', 'password' => bcrypt('kamil'), 'isAdmin' => 1]);
        $adminUser = User::query()->create(['username' => 'admin', 'password' => bcrypt('admin'), 'isAdmin' => 1]);
        $professorUser = User::query()->create(['username' => 'professor', 'password' => bcrypt('professor'), 'isAdmin' => 1]);



    }
}
