<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $emails = [
            'yurikoaishinselo@uvers.ac.id',
            'asrulpuadi@gmail.com',
            'yongming.ja@gmail.com'
        ];

        foreach ($emails as $email) {
            User::firstOrCreate(
                ['email' => $email],
                [
                    'name' => explode('@', $email)[0]
                ]
            );
        }
    }
}
