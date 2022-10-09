<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class AuthJwtTest extends TestCase
{
    /**
     * A basic unit test example.
     *
     * @return void
     */
    public function testExample()
    {
        $this->assertTrue(true);
    }

    /**
    * Test logout.
    *
    * @return void
    */
    public function testLogin()
    {
        $baseUrl = 'http://127.0.0.1:8000/api/auth/login';
        $username ='tester_manager';
        $password = 'password';

        $response = $this->postJson('POST', $baseUrl, ['username' => $username, 'password' => $password]);

        $response
            ->assertStatus(200)
            ->assertJsonStructure([
                $meta =  [
                    'success',
                    'errors'
                ],
        
                $data =  [
                    'token',
                    'minutes_to_expire'
                ]
            ]);
    }
}
