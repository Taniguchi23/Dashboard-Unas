<?php

namespace Database\Seeders;

use App\Models\Email;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class EmailTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = [
            [
                'email' => "sonnytaniguchilock2@gmail.com"
            ],
            [
                'email' => "giusseppeviera@hotmail.com"
            ],

        ];

        Email::insert($users);
    }
}
