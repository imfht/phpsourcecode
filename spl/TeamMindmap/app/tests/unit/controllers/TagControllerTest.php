<?php

/**
 * 标签控制器测试类
 * Class TagControllerTest
 */
class TagControllerTest extends \TestCase
{
    public function setUp()
    {
        parent::setUp();
        Artisan::call('migrate');
        Artisan::call('db:seed');

        $this->seed('UserTableTestSeeder');
        $this->seed('ProjectTableTestSeeder');
        $this->seed('TagTableTestSeeder');

        $this->TagController = $this->app->make('TagController');
    }

    public function testWithoutPaginateIndex()
    {
        $testProject = Project::firstOrFail();
        $resp = $this->action('get', 'TagController@index', [$testProject['id']])->getData(true);

        $this->assertResponseStatus(200);

        $keys = ['id', 'name', 'project_id'];
        $this->arrayMustHasKeys(head($resp), $keys);
    }

    public function testWithPaginateIndex()
    {
        $req = ['per_page' => 1];

        $testProject = Project::firstOrFail();
        $resp = $this->action('get', 'TagController@index', [$testProject['id']], $req)->getData(true);

        $this->assertResponseStatus(200);

        $keys = ['id', 'name', 'project_id'];
        $this->arrayMustHasKeys(head($resp['data']), $keys);
    }

    /**
     * 按标签分页获取分享
     */
    public function testShowWithPaginate()
    {
        $this->seedDB();
        $testTag = Tag::firstOrFail();
        $testProject = Project::firstOrFail();

        $req = ['per_page' => 1];
        $resp = $this->action('get', 'TagController@show', [$testProject['id'], $testTag['id']], $req)->getData(true);
        $data = $resp['data'];

        $respKeys = ['total', 'per_page', 'current_page', 'last_page', 'from', 'to', 'data'];
        $this->arrayMustHasKeys($resp, $respKeys);

        $sharingKeys = ['id', 'name', 'content', 'project_id', 'creater', 'tags', 'resources'];
        $this->arrayMustHasKeys(head($data), $sharingKeys);

        $createrKeys = ['id', 'username', 'email', 'description', 'head_image'];
        $this->arrayMustHasKeys(head($data)['creater'], $createrKeys);


        if (isset(head($data)['tags'])) {
            $createrKeys = ['id', 'name', 'project_id'];
            $this->arrayMustHasKeys(head(head($data)['tags']), $createrKeys);
        }


        if (isset(head($data)['resources']) && head($data)['resources']) {
            $createrKeys = ['id', 'creater_id', 'filename', 'project_id', 'mime', 'origin_name', 'ext_name'];
            $resources = head($data)['resources'];
            $this->arrayMustHasKeys($resources[0], $createrKeys);
        }

    }

    /**
     * 按标签不分页获取分享
     */
    public function testShowWithoutPaginate()
    {
        $this->seedDB();
        $testTag = Tag::firstOrFail();
        $testProject = Project::firstOrFail();

        $resp = $this->action('get', 'TagController@show', [$testProject['id'], $testTag['id']])->getData(true);
        $data = $resp;


        $sharingKeys = ['id', 'name', 'content', 'project_id', 'creater', 'tags', 'resources'];
        $this->arrayMustHasKeys(head($data), $sharingKeys);

        $createrKeys = ['id', 'username', 'email', 'description', 'head_image'];
        $this->arrayMustHasKeys(head($data)['creater'], $createrKeys);


        if (isset(head($data)['tags'])) {
            $createrKeys = ['id', 'name', 'project_id'];
            $this->arrayMustHasKeys(head(head($data)['tags']), $createrKeys);
        }


        if (isset(head($data)['resources']) && head($data)['resources']) {
            $createrKeys = ['id', 'creater_id', 'filename', 'project_id', 'mime', 'origin_name', 'ext_name'];
            $resources = head($data)['resources'];
            $this->arrayMustHasKeys($resources[0], $createrKeys);
        }

    }

    /**
     * 测试方法： store
     */
    public function testStore()
    {
        $testProject = Project::firstOrFail();
        $tagName = 'test';

        $resp = $this->action('post', 'TagController@store', [$testProject['id']], ['name'=>$tagName]);
        $this->assertResponseOk();

        $respDataArray = $resp->getData(true);
        $this->assertEquals($respDataArray['name'], $tagName);
        $this->assertEquals($respDataArray['id'], Tag::where('name', $tagName)->firstOrFail()['id']);
    }

    /**
     * 测试方法： store , 尝试添加已经存在的标签
     */
    public function testStoreWithExist()
    {
        $testTarget = Tag::firstOrFail();

        $resp = $this->action('post', 'TagController@store', [$testTarget['project_id']], ['name'=>$testTarget['name']]);
        $this->assertResponseOk();

        $respDataArray = $resp->getData(true);
        $this->assertEquals($respDataArray['id'], $testTarget['id']);
    }

    protected function seedDB()
    {
        $this->seed('SharingTableTestSeeder');
        $this->seed('SharingTagTableTestSeeder');
    }


    protected $TagController; //标签控制器实例

}