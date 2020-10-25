<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 14-10-27
 * Time: 下午10:40
 */
class MemberControllerTest extends TestCase
{
    /**
     * 执行一些初始化操作：开启Session功能，填充数据库，模拟用户登录，实例化MemberController
     */
    public function setUp()
    {
        parent::setUp();

        Artisan::call('migrate');
        Artisan::call('db:seed');

        $this->memberController = $this->app->make('MemberController');
    }

    /**
     * 测试MemberController的index方法
     */
    public function testIndex()
    {
        $this->seedDB();
        $testUser = $this->getTestUser();
        $testProject = Project::where('creater_id', $testUser['id'])->firstOrFail();


        $resp = $this->memberController->index($testProject['id']);
        $this->assertEquals(200, $resp->getStatusCode());

        $respDataArray = $resp->getData(true);

        $keys = ['username', 'id', 'email', 'head_image'];
        $this->arrayMustHasEqualKeyValues($respDataArray['creater'], Auth::user()->toArray(), $keys);

        $this->assertTrue($respDataArray['editable']);
        $this->assertTrue(is_array($respDataArray['members']));
        $this->arrayMustHasKeys(head($respDataArray['members']), $keys);
    }

    /**
     * 测试MemberController的store方法, 成功通过用户名添加了新成员
     */
    public function testStoreByNameRight()
    {
        list($testProject, $testAccount) = $this->getStoreTestingData();

        $this->storeByMixed($testAccount['username'], $testProject['id'], $testAccount['id']);

    }

    /**
     * 测试MemberController的store，成功通过用户电子邮件地址添加了新成员
     */
    public function testStoreByEmailRight()
    {
        list($testProject, $testAccount) = $this->getStoreTestingData();

        $this->storeByMixed($testAccount['email'], $testProject['id'], $testAccount['id']);
    }

    /**
     * 测试MemberController的store方法，尝试通过不存在的用户名来添加
     */
    public function testStoreByNameWrong()
    {
        list($testProject, $testAccount) = $this->getStoreTestingData();

        $this->setModelNotFoundException();
        $this->storeByMixed($testAccount['username'] . 'not such username', $testProject['id'], $testAccount['id']);
    }

    /**
     * 测试MemberController的store方法，尝试通过不存在的用户电子邮件地址来添加
     */
    public function testStoreByEmailWrong()
    {
        list($testProject, $testAccount) = $this->getStoreTestingData();

        $this->setModelNotFoundException();
        $this->storeByMixed($testAccount['email'] . 'not such email', $testProject['id'], $testAccount['id']);
    }

    /**
     * 没有添加的权限，因为当前的用户不是创建者也不是管理员
     */
    public function testStoreAuthWrong()
    {
        $this->seedDB();
        $testUser = $this->getTestUser();
        $testProject = Project::where('creater_id', '<>', $testUser['id'])->firstOrFail();

        $postData = [
            'memberAccount'=>$testUser['username'],
            'role_id'=>ProjectRole::firstOrFail()['id']
        ];

        $resp = $this->action('post', 'MemberController@store', [ $testProject['id'] ], $postData);
        $this->assertResponseStatus(403);
        $this->assertNotEmpty($resp->getData(true)['error']);
    }

    /**
     * 企图重复添加以存在的成员，操作应该是失败的
     */
    public function testStoreRepeatWrong()
    {
        $this->seedDB();
        $this->getTestUser();

        $testProject = Project::firstOrFail();
        $testMember = $testProject->members()->firstOrFail();

        $postData = [
            'memberAccount' => $testMember['username'],
            'role_id' => ProjectRole::firstOrFail()['id']
        ];
        $resp = $this->action('post', 'MemberController@store', $testProject['id'], $postData);
        $this->assertResponseStatus(403);
        $this->assertTrue(is_string($resp->getData(true)['error']));
    }

    public function testStoreMyselfWrong()
    {
        $this->seedDB();
        $testUser = $this->getTestUser();

        $testProject = Project::firstOrFail();

        $postData = [
            'memberAccount'=>$testUser['username'],
            'role_id'=>ProjectRole::firstOrFail()['id']
        ];

        $resp = $this->action('post', 'MemberController@store', $testProject['id'], $postData);
        $this->assertResponseStatus(403);
        $this->assertTrue(is_string($resp->getData(true)['error']));
    }

    /**
     * 成功更改成员的角色.
     */
    public function testUpdateRight()
    {
        list($testProject) = $this->getStoreTestingData();
        $this->seed('ProjectMemberTableTestSeeder');
        $testMember = $testProject->members()->firstOrFail();

        $putData['role_id'] = ProjectRole::firstOrFail()['id'];
        $this->action('put', 'MemberController@update', [$testProject['id'], $testMember['id']], $putData);
        $this->assertResponseOk();
    }

    /**
     * 更改成员角色失败，该角色不存在.
     */
    public function testUpdateNotFoundWrong()
    {
        list($testProject) = $this->getStoreTestingData();
        $this->seed('ProjectMemberTableTestSeeder');
        $testMember = $testProject->members()->firstOrFail();

        $putData['role_id'] = ProjectRole::count() + 9999;
        $this->setModelNotFoundException();
        $this->action('put', 'MemberController@update', [$testProject['id'], $testMember['id']], $putData);
    }


    /**
     * 内部使用，通过符合的值来添加新成员
     *
     * @param $mixed 用户名或邮箱
     * @param $projectId 项目id
     * @param $memberId 对应的成员id
     */
    private function storeByMixed($mixed,  $projectId, $memberId)
    {
        $postData['memberAccount'] = $mixed;
        $postData['role_id'] = ProjectRole::firstOrFail()['id'];

        $this->action('post', 'MemberController@store', $projectId, $postData);
        $this->assertResponseOk();
        $this->assertNotEmpty(
            Project_Member::where('project_id', $projectId)
                ->where('member_id', $memberId)
                ->first()
        );
    }

    /**
     * 得到用于测试store方法的数据，并填从数据库,注意项目由当前模拟登陆的用户所创建.
     *
     * @return array 返回用于测试的Project模型实例、用于测试的User模型实例（待添加的成员)
     */
    private function getStoreTestingData(){
        $this->seed('UserTableTestSeeder');
        $this->seed('ProjectTableTestSeeder');

        $testUser = $this->getTestUser();
        $testProject = Project::where('creater_id', $testUser['id'])->firstOrFail();
        $testAccount = User::where('id', '<>', $testUser['id'])->firstOrFail();

        return  [$testProject, $testAccount];
    }

    /**
     * 测试MemberController的show方法
     */
    public function testShow()
    {
        $this->seedDB();
        $testProject = Project::firstOrFail();
        $testMember = $testProject['members']->first();

        $resp = $this->memberController->show($testProject['id'], $testMember['id']);
        $this->assertEquals(200, $resp->getStatusCode());
        $respDataArray = $resp->getData(true);

        $keys = ['id', 'username', 'email', 'head_image', 'role_id', 'role_label'];
        $this->arrayMustHasKeys($respDataArray, $keys, true);
    }

    /**
     * 测试MemberController方法destroy，尝试删除不存在的成员.
     */
    public function testDestroyNotFoundWrong()
    {
        $testUser = $this->getTestUserAndSeedDB();
        $testProject = $testUser->createProjects()->firstOrFail();

        $resp = $this->memberController->destroy($testProject['id'],  9999);
        $this->assertEquals(404, $resp->getStatusCode());
    }

    /**
     * 测试MemberController方法destroy，尝试进行没有操作权限的删除
     */
    public function testDestroyAuthWrong()
    {
        $testUser = $this->getTestUserAndSeedDB();
        $testProject = Project::where('creater_id', '<>', $testUser['id'])->firstOrFail();
        $testMember = $testProject['creater'];

        $resp = $this->memberController->destroy($testProject['id'],  $testMember['id']);
        $this->assertEquals(403, $resp->getStatusCode());

    }

    /**
     * 成功进行了删除.
     */
    public function testDestroyRight()
    {
        $testUser = $this->getTestUserAndSeedDB();
        $testProject = $testUser->createProjects()->firstOrFail();
        $testMember = $testProject->members()->firstOrFail();

        $resp = $this->memberController->destroy($testProject['id'],  $testMember['id']);
        $this->assertEquals(200, $resp->getStatusCode());

    }

    /**
     * 内部使用，用于填充必要的数据库数据
     */
    private function seedDB()
    {
        $this->seed('UserTableTestSeeder');
        $this->seed('ProjectTableTestSeeder');
        $this->seed('ProjectMemberTableTestSeeder');
    }

    /**
     * 内部使用，用于填充必要的数据库数据，返回获取到的测试用户（并登陆）
     * @return \Illuminate\Database\Eloquent\Model|MemberControllerTest|static
     */
    private function getTestUserAndSeedDB()
    {
        $this->seedDB();
        return $this->getTestUser();
    }

    private $memberController;	//引用MemberController的实例
}
