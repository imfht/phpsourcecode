<?php
/**
 * Created by PhpStorm.
 * User: dust2
 * Date: 15-1-29
 * Time: 下午7:42
 */

class ProjectSharingEventHandlerTest extends TestCase
{
    public function setUp()
    {
        parent::setUp();

        Artisan::call('migrate');

        $this->testUser = $this->getTestUser(true);

        $this->seed('ProjectTableTestSeeder');
        $this->seed('NotifyTypeTableSeeder');
        $this->seedDB();

        $this->projectDiscussionController = $this->app->make('ProjectSharingController');
    }


    /**
     * 测试方法： store,创建分享, 只附带必须的域
     */
    public function testSharingCreatedWithMustField()
    {
        $postData = [
            'name' => 'testSharingName',
            'content' => 'testSharingContent',
        ];

        $this->SharingCreatedTemplate($postData);

    }


    /**
     * 测试方法： store,创建分享, 附带资源
     */
    public function testSharingCreatedWithResource()
    {
        $postData = [
            'name' => 'testSharingName',
            'content' => 'testSharingContent',
            'resource' => ['filename'=>'mockFilename', 'origin_name'=>'origin_name', 'ext_name'=>'jpg', 'mime'=>'image/jpeg']
        ];

        $this->SharingCreatedTemplate($postData);

    }

    /**
     * 测试方法： store,创建分享, 附带标签（以及资源）
     */
    public function testSharingCreatedWithTags()
    {
        $testProject = Project::firstOrFail();
        $postData = [
            'name' => 'sharingNameWithTag',
            'content' => 'sharingContentWithTag',
            'resource' => ['filename'=>'mockFilename', 'origin_name'=>'origin_name', 'ext_name'=>'jpg', 'mime'=>'image/jpeg'],
            'tag' => Tag::insertGetId([
                'name' => str_random(4),
                'project_id' => $testProject['id'],
                'created_at' => date('Y-m-d h:m:s'),
                'updated_at' => date('Y-m-d h:m:s')
            ])
        ];

        $this->SharingCreatedTemplate($postData);

    }

    /**
     * 获取通知测试模板
     * @param $postData
     */
    protected function SharingCreatedTemplate($postData)
    {
        $this->seedDB();
        $testProject = Project::firstOrFail();

        $oldCount = $this->initNotifyCount();

        $resp = $this->action('post', 'ProjectSharingController@store', [$testProject['id']], $postData);
        $this->assertResponseOk();

        $newCount['notify'] = Notification::where('type_id', $this->testType)->count();
        $this->assertEquals($oldCount['notify'] + 1, $newCount['notify']);

        $newCount['inbox'] = NotifyInbox::where('receiver_id', $this->testUser['id'])->count();
        $this->assertEquals($oldCount['inbox'] + 1, $newCount['inbox']);
    }

    protected function seedDB()
    {
        $this->seed('ResourceTableTestSeeder');
        $this->seed('SharingResourceTableTestSeeder');
        $this->seed('TagTableTestSeeder');
        $this->seed('SharingTagTableTestSeeder');
    }

    /**
     * 获得通知添加之前的Notification和NotifyInbox的记录数，用于验证是否成功添加通知
     *
     * @return $oldCount array 关联数组，存储Notification和NotifyInbox中的记录数
     */
    public function initNotifyCount()
    {
        $oldCount['notify'] = Notification::where('type_id', $this->testType)->count();
        $oldCount['inbox'] = NotifyInbox::count();
        return $oldCount;
    }

    /**
     * 通过对比记录数目，检查是否成功添加通知记录
     *
     * @param $oldCount array 关联数组，存储Notification和NotifyInbox中的记录数
     * @param $sourceId int 暂定为项目的id
     */
    public function checkCount($oldCount, $sourceId)
    {
        $newCount = Notification::where('type_id', $this->testType)->count();
        $this->assertEquals($oldCount['notify'] + 1, $newCount);

        $memberCount = Project_Member::where('project_id', $sourceId)->count();
        $inboxCount = NotifyInbox::count();
        $this->assertEquals($oldCount['inbox'] + $memberCount + 1, $inboxCount);
    }

    private $testType = 5;

    private $projectDiscussionController;

    private $testUser;
}