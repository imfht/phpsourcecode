<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Validator;

class TestQuestion extends TestCase
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
    }

    public function getToken()
    {
        return file_get_contents('./token.txt');
    }

    public function testSuccessGetTaskContent(){
        $token = $this->getToken();

        $data = $this->post('/api/get-task-content',[
            'taskId' => 2,
        ],['authorization'=>'bearer '.$token])->response->getContent();

        $this->validate($data, [
            'error' => 'required|in:0|integer',
            'content' => 'required|array'
        ]);
    }

    public function testFailGetTaskContent(){
        $token = $this->getToken();

        $this->post('/api/get-task-content',[],['authorization'=>'bearer '.$token])->seeJson([
            'error'=> 'bad_parameter'
        ]);
    }

    public function testSuccessAddQuestion(){
        $token = $this->getToken();

        $this->post('/api/add-question',[
            'content' => '{"question":"byUGsn9I9p5GdJSwLslxPKZHm0L4xL","options":["Ast4OBUstzWKj0Ad262E","W0V5lMCt3BJhToLysm2K","ARhOiK95OcmHIxH6PVPE","FwqFgSDcxiE1AOeRVPkE"]}',
            'answers' => 2
        ],['authorization'=>'bearer '.$token])
            ->seeJson([
                'error' => 0
            ]);
    }

    public function testFailAddQuestion(){
        $token = $this->getToken();

        $this->post('/api/add-question',[
            'answers' => 2
        ],['authorization'=>'bearer '.$token])
            ->seeJson([
                'error' => 'bad_parameter'
            ]);
    }

    public function testSuccessDeleteQuestion(){
        $token = $this->getToken();

        $this->post('/api/add-question',[
            'content' => '{"question":"byUGsn9I9p5GdJSwLslxPKZHm0L4xL","options":["Ast4OBUstzWKj0Ad262E","W0V5lMCt3BJhToLysm2K","ARhOiK95OcmHIxH6PVPE","FwqFgSDcxiE1AOeRVPkE"]}',
            'answers' => 2
        ],['authorization'=>'bearer '.$token])
            ->seeJson([
                'error' => 0
            ]);

        $question = json_decode($this->response->getContent(),true);

        $this->post('/api/del-question',[
            'questionId'=>$question['id']
        ],['authorization'=>'bearer '.$token])
            ->seeJson([
                'error' => 0
            ]);
    }

    public function testFailDeleteQuestion1(){
        $token = $this->getToken();

        $this->post('/api/del-question',[],['authorization'=>'bearer '.$token])
            ->seeJson([
                'error' => 'bad_parameter'
            ]);
    }

    public function testFailDeleteQuestion2(){
        $token = $this->getToken();

        $this->post('/api/del-question',[
            'questionId'=>1
        ],['authorization'=>'bearer '.$token])
            ->seeJson([
                'error' => 'db_cannot_delete'
            ]);
    }

    public function testSuccessUpdateQuestion(){
        $token = $this->getToken();

        $this->post('/api/add-question',[
            'content' => '{"question":"byUGsn9I9p5GdJSwLslxPKZHm0L4xL","options":["Ast4OBUstzWKj0Ad262E","W0V5lMCt3BJhToLysm2K","ARhOiK95OcmHIxH6PVPE","FwqFgSDcxiE1AOeRVPkE"]}',
            'answers' => 2
        ],['authorization'=>'bearer '.$token])
            ->seeJson([
                'error' => 0
            ]);

        $question = json_decode($this->response->getContent(),true);

        $this->post('/api/update-question',[
            'questionId' => $question['id'],
            'content' => 'abcd',
            'answers' => 2
        ],['authorization'=>'bearer '.$token])
            ->seeJson([
            'error' => 0
        ]);
    }

    public function testFailUpdateQuestion(){
        $token = $this->getToken();

        $this->post('/api/add-question',[
            'content' => '{"question":"byUGsn9I9p5GdJSwLslxPKZHm0L4xL","options":["Ast4OBUstzWKj0Ad262E","W0V5lMCt3BJhToLysm2K","ARhOiK95OcmHIxH6PVPE","FwqFgSDcxiE1AOeRVPkE"]}',
            'answers' => 2
        ],['authorization'=>'bearer '.$token])
            ->seeJson([
                'error' => 0
            ]);

        $question = json_decode($this->response->getContent(),true);

        $this->post('/api/update-question',[
            'questionId' => $question['id'],
            'answers' => 2
        ],['authorization'=>'bearer '.$token])
            ->seeJson([
                'error' => 'bad_parameter'
            ]);
    }

    public function testSuccessGetAllQuestions(){
        $token = $this->getToken();

        $data = $this->post('/api/get-all-question',[
            'offset'=>10,
            'limit'=>10,
            'search'=>'',
        ],['authorization'=>'bearer '.$token])->response->getContent();

        $this->validate($data,[
            'error' => 'required|in:0|integer',
            'total' => 'required|integer',
            'rows' => 'required|array'
        ]);
    }

    public function testFailGetAllQuestions(){
        $token = $this->getToken();

        $this->post('/api/get-all-question',[
            'offset'=>10,
        ],['authorization'=>'bearer '.$token])->seeJson([
            'error' => 'bad_parameter'
        ]);
    }
}
