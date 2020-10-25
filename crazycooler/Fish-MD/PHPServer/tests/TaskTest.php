<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Validator;

class TestTask extends TestCase
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

    public function getToken()
    {
        return file_get_contents('./token.txt');
    }

    public function testSuccessGetAllDataByType(){
        $token = $this->getToken();
        $data = $this->post('/api/get-task-list-by-group-id-and-type',['taskType' => 1],['authorization'=>'bearer '.$token])->response->getContent();

        $this->validate($data, [
            'error' => 'required|in:0|integer',
            'list' => 'required|array'
        ]);
    }

    public function testFailGetTasks(){
        $token = $this->getToken();
        $this->post('/api/get-task-list-by-group-id-and-type',[],['authorization'=>'bearer '.$token])->seeJson([
            'error' => 'bad_parameter'
        ]);
    }

    public function testSuccessGetAllData(){
        $token = $this->getToken();
        $data = $this->post('/api/get-task-list-by-group-id',[],['authorization'=>'bearer '.$token])->response->getContent();

        $this->validate($data, [
            'error' => 'required|in:0|integer',
            'list' => 'required|array'
        ]);
    }


    public function testSuccessGetAllTasks()
    {
        $token = $this->getToken();

        $data = $this->post('/api/get-all-tasks',[
            'offset' => 0,
            'limit' => 10,
        ],['authorization'=>'bearer '.$token])->response->getContent();

        $this->validate($data, [
            'error' => 'required|in:0|integer',
            'total' => 'required|integer',
            'rows' => 'required|array',
        ]);
    }

    public function testSuccessPublishTask()
    {
        $token = $this->getToken();

        $this->post('/api/publish-task',[
            'taskId' => 10
        ],['authorization'=>'bearer '.$token])->seeJson([
            'error' => 0
        ]);
    }

    public function testSuccessAddTask()
    {
        $token = $this->getToken();

        $this->post('/api/add-task',[
            'title' => '1230',
            'target' => 'nothing',
            'week' => 100,
            'type' => 2,
            'groupId' => 4,
            'startTime'=> '1993-03-08 10:59:11',
            'deadLine' => '1993-08-08 10:59:11',
            'questions' => [5,6,7,8],
        ],['authorization'=>'bearer '.$token])->seeJson([
            'error' => 0
        ]);

        $task = json_decode($this->response->getContent(),true);

        $this->post('/api/del-task',[
            'taskId' => $task['taskId']
        ],['authorization'=>'bearer '.$token])->seeJson([
            'error' => 0
        ]);
    }

    public function testSuccessAddTask2()
    {
        $token = $this->getToken();

        $this->post('/api/add-task',[
            'title' => '345345',
            'target' => 'nothineeg',
            'week' => 100,
            'type' => 4,
            'groupId' => 4,
            'startTime'=> '1993-03-08 10:59:11',
            'deadLine' => '1993-08-08 10:59:11',
            'questions' => [],
        ],['authorization'=>'bearer '.$token])->seeJson([
            'error' => 0
        ]);

        $task = json_decode($this->response->getContent(),true);

        $this->post('/api/del-task',[
            'taskId' => $task['taskId']
        ],['authorization'=>'bearer '.$token])->seeJson([
            'error' => 0
        ]);
    }

    public function testSuccessUpdateTask()
    {
        $token = $this->getToken();

        $this->post('/api/add-task',[
            'title' => '345345',
            'target' => 'nothineeg',
            'week' => 100,
            'type' => 2,
            'groupId' => 4,
            'startTime'=> '1993-03-08 10:59:11',
            'deadLine' => '1993-08-08 10:59:11',
            'questions' => [5,6,7,8],
        ],['authorization'=>'bearer '.$token])->seeJson([
            'error' => 0
        ]);

        $task = json_decode($this->response->getContent(),true);

        $this->post('/api/update-task',[
            'taskId' => $task['taskId'],
            'title' => '666',
            'target' => 'ggg',
            'week' => 100,
            'type' => 4,
            'groupId' => 4,
            'startTime'=> '1993-03-08 10:59:11',
            'deadLine' => '1993-08-08 10:59:11',
            'questions' => [8,9,10],
        ],['authorization'=>'bearer '.$token])->seeJson([
            'error' => 0
        ]);


        $this->post('/api/del-task',[
            'taskId' => $task['taskId']
        ],['authorization'=>'bearer '.$token])->seeJson([
            'error' => 0
        ]);
    }

}
