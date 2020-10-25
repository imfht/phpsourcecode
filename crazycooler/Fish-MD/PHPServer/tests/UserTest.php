<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Validator;

class TestUser extends TestCase
{

    protected function validate($obj,$rule)
    {
        $signInArr = json_decode($obj,true);

        $validator = Validator::make($signInArr,$rule);

        if($validator->fails()){
            $this->fail($validator->errors().' in '.$obj);
        } else {
            $this->assertTrue(true);
        }

        return $signInArr;
    }

    public function getToken()
    {
        return file_get_contents('./token.txt');
    }


    public function testSuccessGetAllUsers()
    {
        $token = $this->getToken();
        $data = $this->post('/api/get-all-users',[
            'offset' => 0,
            'limit' => 10,
        ],['authorization'=>'bearer '.$token])->response->getContent();



        $this->validate($data, [
            'error' => 'required|in:0|integer',
            'total' => 'required|integer',
            'rows' => 'required|array',
        ]);
    }

    public function testSuccessGetAllUsers2()
    {
        $token = $this->getToken();
        $data = $this->post('/api/get-all-users',[
            'offset' => 0,
            'limit' => 10,
            'groupId' => 1,
            'class' => 2,
        ],['authorization'=>'bearer '.$token])->response->getContent();


        $users = $this->validate($data, [
            'error' => 'required|in:0|integer',
            'total' => 'required|integer',
            'rows' => 'required|array',
        ]);

        foreach($users['rows'] as $item){
            if(!($item['class'] == 2 && $item['groupId'] == 1)){
                $this->fail('class and groupId field is bad, in '.$data);
            }
        }
    }

    public function testSuccessGetUserDetail()
    {
        $token = $this->getToken();
        $data = $this->post('/api/get-user-detail',[
            'id' => 1,
        ],['authorization'=>'bearer '.$token])->response->getContent();


        $this->validate($data, [
            'user' => 'required|array',
            'user.id' =>'required|in:1',
            'tasks' => 'array',
        ]);
    }

    public function testSuccessGetUserDetail2()
    {
        $token = $this->getToken();
        $data = $this->post('/api/get-user-detail',[
            'stuId' => 108,
        ],['authorization'=>'bearer '.$token])->response->getContent();


        $this->validate($data, [
            'user' => 'required|array',
            'user.stuId' =>'required|in:108',
            'tasks' => 'array',
        ]);
    }

    public function testFailGetUserDetai()
    {
        $token = $this->getToken();
        $this->post('/api/get-user-detail',[
            'stuId' => 666,
        ],['authorization'=>'bearer '.$token])->seeJson([
            'error' => 'not_find_the_user'
        ]);
    }

    public function testSuccessUpdateUser()
    {
        $token = $this->getToken();
        $this->post('/api/update-user',[
            'userId' => 1,
            'groupId' => 4,
        ],['authorization'=>'bearer '.$token])->seeJson([
            'error' => 0
        ]);
    }

    public function testFailUpdateUser()
    {
        $token = $this->getToken();
        $this->post('/api/update-user',[
            'userId' => 100,
            'groupId' => 4,
        ],['authorization'=>'bearer '.$token])->seeJson([
            'error' => 'update_fail'
        ]);
    }
}
