<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

//must run this test first,to create token for next test

class CreateTokenTest extends TestCase
{
    public function testGetToken()
    {
        $signIn = $this->post('/api/sign-in',[
            'stuId' => '108',
            'password' => '666',
        ])->response->getContent();

        $signInArr = json_decode($signIn,true);

        file_put_contents('./token.txt',$signInArr['token']);

        $this->assertTrue(true);
    }
}
