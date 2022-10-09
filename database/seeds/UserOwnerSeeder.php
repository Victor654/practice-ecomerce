<?php

use Illuminate\Database\Seeder;

class UserOwnerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert([
            'username' => "tester_agent",
            'password' => "PASSWORDAGENT",
            'is_active' => "true",
            'role' => "agent"
        ]);
    }
}
