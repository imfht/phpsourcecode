<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;


class AuthTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    protected function validate($obj,$rule)
    {
        $signInArr = json_decode($obj,true);

        $validator = Validator::make($signInArr,$rule);

        if($validator->fails()){
            $this->fail($validator->errors().' in '.$obj);
        } else {
            $this->assertTrue(true);
        }
    }

    public function testSuccessSignIn()
    {
        $signIn = $this->post('/api/sign-in',[
            'stuId' => '111',
            'password' => '666',
        ])->response->getContent();

        $this->validate($signIn,[
            'error'=>'required|in:0|integer',
            'userInfo'=>'required|array',
            'token'=>'required|string|min:100',
        ]);
    }

    public function testFailSignIn()
    {
        $this->post('/api/sign-in',[
            'stuId' => '111',
            'password' => '7896',
        ])->seeJson([
            'error' => 'invalid_credentials'
        ]);
    }

    public function testBadParameter()
    {
        $this->post('/api/sign-in',[
            'stuId123' => '111',
        ])->seeJson([
            'error' => 'bad_parameter'
        ]);
    }

    public function testSuccessRefresh()
    {
        $signIn = $this->post('/api/sign-in',[
            'stuId' => '111',
            'password' => '666',
        ])->response->getContent();

        $signInArr = json_decode($signIn,true);

        $refresh = $this->post('/api/refresh',[
            'token' => $signInArr['token']
        ])->response->getContent();

        $this->validate($refresh,[
            'error'=>'required|in:0|integer',
            'token'=>'required|string|min:100',
        ]);
    }

    public function testFailRefresh()
    {
        $this->post('/api/refresh',[
            'token' => '123456'
        ])->seeJson([
            'error' => 'bad_token_to_refresh'
        ]);
    }

    public function testSuccessSignOut()
    {
        $signIn = $this->post('/api/sign-in',[
            'stuId' => '111',
            'password' => '666',
        ])->response->getContent();

        $signInArr = json_decode($signIn,true);

        $this->post('/api/sign-out',[
            'token' => $signInArr['token']
        ])->seeJson(['error' => 0]);
    }

    public function testFailSignOut()
    {
        $this->post('/api/sign-out',[
            'token' => '8234829379kewr'
        ])->seeJson(['error' => 0]);
    }

    public function getToken()
    {
        return file_get_contents('./token.txt');
    }

    public function testUser()
    {
        $token = $this->getToken();
        $user = $this->post('/api/user',[],['authorization'=>'bearer '.$token])->response->getContent();

        $this->validate($user,[
            'error'=>'required|in:0|integer',
            'userInfo'=>'required|array',
        ]);
    }
}
