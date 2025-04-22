<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\User::create([
            'firstname' => 'toto',
            'lastname' => 'toto',
            'email' => 'toto@toto.fr',
            'phone' => '0612345678',
            'password' => Hash::make('totototo'),
            'address' => '1 rue de la paix, Paris',
            'postal_code' => '75001',
            'city' => 'Paris',
            'country' => 'France',
        ]);
    }
}
