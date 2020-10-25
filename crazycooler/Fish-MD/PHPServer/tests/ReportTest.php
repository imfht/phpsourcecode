<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Validator;

class TestReport extends TestCase
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

    public function testSuccessSubmitTaskType1_3(){
        $token = $this->getToken();

        $data = $this->post('/api/add-task-report',[
            'taskId' => 5,
            'content' => '{21:1,22:2,23:3,24:2,25:2}',
            'type' => 1
        ],['authorization'=>'bearer '.$token])->response->getContent();

        $this->validate($data, [
            'error' => 'required|in:0|integer',
            'score' => 'required|integer'
        ]);
    }

    public function testSuccessSubmitTaskType4(){
        $token = $this->getToken();

        $this->post('/api/add-task-report',[
            'taskId' => 25,
            'content' => '{"content":"总结总结总结总结总结总结总结总结总结","score":5}',
            'type' => 4
        ],['authorization'=>'bearer '.$token])->seeJson([
            'error' => 0
        ]);
    }

    public function testSuccessGetReports(){
        $token = $this->getToken();

        $data = $this->post('/api/get-reports',[
            'offset'=>0,
            'limit'=>10,
            'search'=>'',
            'taskId' => '1',
            'teacherId' => '9'
        ],['authorization'=>'bearer '.$token])->response->getContent();

        $this->validate($data,[
            'error' => 'required|in:0|integer',
            'total' => 'required|integer',
            'rows' => 'required|array'
        ]);
    }

    public function testFailGetReports(){
        $token = $this->getToken();

        $this->post('/api/get-reports',[
            'offset'=>10,
            'limit'=>10,
            'search'=>'',
            'teacherId' => '9'
        ],['authorization'=>'bearer '.$token])->seeJson([
            'error' => 'bad_parameter'
        ]);
    }

    public function testSuccessGradeReport(){
        $token = $this->getToken();

        $this->post('/api/grade-reports',[
            'score' => 10,
            'userId' => 9,
            'taskId' => 25
        ],['authorization'=>'bearer '.$token])->seeJson([
            'error' => 0
        ]);

    }

    public function testFailGradeReport(){
        $token = $this->getToken();

        $this->post('/api/grade-reports',[
            'score' => 10,
        ],['authorization'=>'bearer '.$token])->seeJson([
            'error' => 'bad_parameter'
        ]);
    }

}














