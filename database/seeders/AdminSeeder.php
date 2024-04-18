<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        User::Create([
            'name' => 'admin',
            'email'=>'admin@vshop.com',
            'password' => Hash::make('123456789'),
            'isAdmin' =>1
        ]);

        
        


    }
}
