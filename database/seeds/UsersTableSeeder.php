<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert([
            'name' => 'admin',
            'issuer_title' => 'Technische Hochschule Lübeck',
            'email' => 'secret@secret.com',
            'password' => bcrypt('secret'),
        ]);
    }
}
