<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 14-12-28
 * Time: 下午6:46
 */
class ProjectDiscussionEventHandlerTest extends TestCase
{
    public function setUp()
    {
        parent::setUp();

        Artisan::call('migrate');

        $this->testUser = $this->getTestUser(true);
        $this->seedDB();

        $this->projectDiscussionController = $this->app->make('ProjectDiscussionController');
    }

    public function testDiscussionCreated()
    {
        $testProject = Project::firstOrFail();
        $postData = [
            'title'=>'just take a test',
            'content'=>'just a test content',
            'followers'=>[2, 3]
        ];

        $oldCount = $this->initNotifyCount();

        $resp = $this->action('post', 'ProjectDiscussionController@store', [ $testProject['id'] ], $postData);
        $this->assertResponseOk();

        $newCount['notify'] = Notification::where('type_id', $this->testType)->count();
        $this->assertEquals($oldCount['notify'] + 2, $newCount['notify']);

        $newCount['inbox'] = ProjectDiscussionFollower::count();
        $this->assertEquals($oldCount['inbox'] + 2, $newCount['inbox']);
    }

    public function testDiscussionUpdated()
    {
        $this->seed('ProjectDiscussionsTableTestSeeder');

        $testProject = Project::where('creater_id', $this->testUser['id'])->firstOrFail();
        $testDiscussion = ProjectDiscussion::where('project_id', $testProject['id'])->firstOrFail();

        $putData = [
            'open'=> ! $testDiscussion['open']
        ];

        $oldCount = $this->initNotifyCount();

        $this->action('put', 'ProjectDiscussionController@update', [ $testProject['id'], $testDiscussion['id'] ], $putData);
        $this->assertResponseOk();

        $this->checkCount($oldCount, $testProject['id']);
    }

    protected function seedDB()
    {
        $this->seed('NotifyTypeTableSeeder');
        $this->seed('ProjectTableTestSeeder');
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

    private $testType = 4;

    private $projectDiscussionController;

    private $testUser;
}