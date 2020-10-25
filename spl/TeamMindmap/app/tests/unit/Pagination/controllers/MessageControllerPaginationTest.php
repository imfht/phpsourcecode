<?php
/**
 * Created by PhpStorm.
 * User: dust2
 * Date: 15-3-6
 * Time: 下午9:59
 */

/**
 * Class MessageControllerTest
 * 私信控制器测试类
 */
class MessageControllerPaginationTest extends \TestCase
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
     * 测试分页时获取用户关联的私信中的 received 类别
     */
    public function testGetReceivedMessages()
    {
        $this->seedDB();

        $resq = ['per_page' => 10, 'option' => 'received'];
        $resp = $this->action('get', 'MessageController@index', null ,$resq);

        $this->assertEquals(200, $resp->getStatusCode());

        $respDataArray = $resp->getData(true);

        $this->assertCount(2, $respDataArray['data']);
        $receivedKeys = ['id', 'title', 'sender_id', 'sender_username', 'sender_email', 'sender_head_image', 'content', 'created_at', 'read'];
        $receivedData = head( $respDataArray['data']);
        $this->arrayMustHasKeys($receivedData, $receivedKeys, true );
    }

    /**
     * 测试分页时获取用户关联的私信中的 sent　类别
     */
    public function testGetSentMessages()
    {
        $this->seedDB();

        $resq = ['per_page' => 10, 'option' => 'sent'];
        $resp = $this->action('get', 'MessageController@index', null ,$resq);

        $this->assertEquals(200, $resp->getStatusCode());
        $respDataArray = $resp->getData(true);
        $this->assertCount(1, $respDataArray['data']);

        $sentKeys = ['id', 'title', 'receiver_id', 'receiver_username', 'receiver_email', 'receiver_head_image', 'content', 'created_at', 'read'];
        $sentData = head( $respDataArray['data']);
        $this->arrayMustHasKeys($sentData , $sentKeys, true);
    }




    protected function seedDB()
    {
        $this->seed('MessagesTableTestSeeder');
        $this->seed('MessageInboxsTableTestSeeder');
    }

    protected $MessageController; //私信控制器
    protected $currUser; //当前登入用户
}