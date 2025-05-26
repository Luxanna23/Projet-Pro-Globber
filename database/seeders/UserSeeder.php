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
            'email' => 'toto@gmail.com',
            'phone' => '0612345678',
            'password' => Hash::make('totototo'),
            'address' => '1 rue de la paix, Paris',
            'postal_code' => '75001',
            'city' => 'Paris',
            'country' => 'France',
        ]);

        \App\Models\User::create([
            'firstname' => 'John',
            'lastname' => 'dada',
            'email' => 'dada@gmail.com',
            'phone' => '0612365678',
            'password' => Hash::make('totototo'),
            'address' => '1 rue de la paix, Paris',
            'postal_code' => '75001',
            'city' => 'Paris',
            'country' => 'France',
        ]);

        ///des users pour laisser des commentaires

        \App\Models\User::create([
            'firstname' => 'Jeremy',
            'lastname' => 'Doe',
            'email' => 'jeje@gmail.com',
            'phone' => '0612365678',
            'password' => Hash::make('totototo'),
            'address' => '1 rue de la paix, Paris',
            'postal_code' => '75001',
            'city' => 'Paris',
            'country' => 'France',
        ]);

        \App\Models\User::create([
            'firstname' => 'Florian',
            'lastname' => 'Dae',
            'email' => 'flo@gmail.com',
            'phone' => '0612365678',
            'password' => Hash::make('totototo'),
            'address' => '1 rue de la paix, Paris',
            'postal_code' => '75001',
            'city' => 'Paris',
            'country' => 'France',
        ]);
    }
}
