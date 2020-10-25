<?php
/**
 * Created by PhpStorm.
 * User: spatra
 * Date: 14-12-11
 * Time: 上午10:06
 */

/**
 * Class MessageControllerTest
 * 私信控制器测试类
 */
class MessageControllerTest extends \TestCase
{
    public function setUp()
    {
        parent::setUp();
        Artisan::call('migrate');
        Artisan::call('db:seed');

        $this->currUser = $this->getTestUser(true);

        $this->MessageController = $this->app->make('MessageController');
    }


    /**
     * 用户可以根据id、用户名、邮箱进行私信,
     * 这里暂时作为扩展测试用例
     */
    public function testStoreSingleSuccessWithUserName()
    {
        $this->testStoreSingleSuccessWith('spatra');
    }

    public function testStoreSingleSuccessWithEmail()
    {
        $this->testStoreSingleSuccessWith('spatra@sp.com');
    }

    /**
     * 测试一对一发送私信成功
     */
    public function testStoreSingleSuccessWith($receiver = 2)
    {
        $postData['receiver_id'] = $receiver;
        $postData['title'] = 'title';
        $postData['content'] = 'content created by'.$this->currUser['id'];
        $resp = $this->call('POST', 'api/messages', $postData);
        $this->assertResponseStatus(200);
        $checkData = $postData;
        $checkData['sender_id'] = $this->currUser['id'];

        //检查是否存入发件箱
        $sentKeys = ['sender_id', 'title', 'content'];
        $newSentMessage = Message::findOrFail($resp->getData(true)['id'])->toArray();
        $this->arrayMustHasEqualKeyValues($checkData, $newSentMessage, $sentKeys);

        //检查是否存入收件箱
        $checkData['read'] = 0;
        $checkData['message_id'] = 1;
        $checkData['receiver_id'] = 2;
        $receivedKeys = ['message_id', 'receiver_id', 'read'];
        $newReceivedMessage = MessageInbox::findOrFail($resp->getData(true)['id'])->toArray();
        $this->arrayMustHasEqualKeyValues($checkData, $newReceivedMessage, $receivedKeys);
    }

    /**
     * 测试更新
     */
    public function testUpdateRight()
    {
        $this->seedDB();
        $testMsg = MessageInbox::where('receiver_id', $this->currUser['id'])
            ->where('read', '<>', true)
            ->firstOrFail();

        $resp = $this->MessageController->update($testMsg['id']);
        $this->assertEquals(200, $resp->getStatusCode());

        $changedMsg = MessageInbox::findOrFail($testMsg['id']);
        $this->assertEquals($changedMsg['read'], true);
    }


    protected function seedDB()
    {
        $this->seed('MessagesTableTestSeeder');
        $this->seed('MessageInboxsTableTestSeeder');
    }

    protected $MessageController; //私信控制器
    protected $currUser; //当前登入用户
}