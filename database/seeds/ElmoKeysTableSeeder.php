<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ElmoKeysTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $keys = [

        ];

        foreach($keys as $key) {
            DB::table('users')->insert([
                'title' => $key,
            ]);
        }
    }
}
