<?php

use Illuminate\Database\Seeder;

class UserManagerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert([
            'username' => "tester_manager",
            'password' => "PASSWORDMANAGER",
            'is_active' => "true",
            'role' => "manager"
        ]);
    }
}
