<?php
/**
 * Created by PhpStorm.
 * User: spatra
 * Date: 15-1-28
 * Time: 下午7:38
 */

class ProjectTest extends TestCase
{
    public function setUp()
    {
        parent::setUp();
        Artisan::call('migrate');
        Artisan::call('db:seed');

        $this->testUser = $this->getTestUser(true, true);
    }

    /**
     * 测试方法： checkManagerOrCreater, 返回结果应该是true
     */
    public function testStaticCheckManagerOrCreaterRight()
    {
        $this->seedDB();

        $testProject = $this->testUser->createProjects()->first();

        $this->assertTrue( Project::checkManagerOrCreater($this->testUser, $testProject));
        $this->assertTrue( Project::checkManagerOrCreater($this->testUser['id'], $testProject['id']));

        $testManager = $this->getManager($testProject['id']);
        $this->assertTrue( Project::checkManagerOrCreater($testManager, $testProject));
        $this->assertTrue( Project::checkManagerOrCreater($testManager['id'], $testProject['id']));
    }

    /**
     * 测试方法： checkManagerOrCreater, 返回结果应该是false
     */
    public function testStaticCheckManagerOrCreaterWrong()
    {
        $this->seedDB();

        $testProject = $this->testUser->joinProjects()->first();

        $this->assertFalse( Project::checkManagerOrCreater($this->testUser, $testProject));
        $this->assertFalse( Project::checkManagerOrCreater($this->testUser['id'], $testProject['id']));
    }

    /**
     * 返回一个测试管理员.
     *
     * @param $projectId
     * @return \Illuminate\Support\Collection|static
     */
    protected function getManager($projectId)
    {
        $managerRoleId = ProjectRole::where('name', 'manager')
            ->firstOrFail()['id'];

        $managerId = Project_Member::where('project_id', $projectId)
            ->where('role_id', $managerRoleId)->firstOrFail()['id'];

        return User::findOrFail($managerId);
    }

    protected function seedDB()
    {
        $this->seed('ProjectTableTestSeeder');
        $this->seed('ProjectMemberTableTestSeeder');
    }

    private $testUser;
}