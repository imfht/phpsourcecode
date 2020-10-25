<?php
/**
 * Created by PhpStorm.
 * User: spatra
 * Date: 14-11-20
 * Time: 上午11:29
 */

/**
 * Class ProjectTaskStatusTest
 *
 * 测试模型类ProjectTaskStatusTest中的自定义方法.
 *
 */
class ProjectTaskStatusTest extends TestCase
{
    /**
     * 初始化，执行数据库迁移
     */
    public function setUp()
    {
        parent::setUp();
        Artisan::call('migrate');
    }

    /**
     * 成功获取
     */
    public function testGetIdByNameRight()
    {
        $testStatus = $this->getTestStatus();

        $testId = ProjectTaskStatus::getIdByName($testStatus['name']);
        $this->assertEquals($testId, $testStatus['id']);
    }

    /**
     * 不能获取
     */
    public function testGetIdByNameWrong()
    {
        $this->assertEmpty( ProjectTaskStatus::getIdByName('doing') );
    }

    /**
     * 成功获取
     */
    public function testGetIdByNameOrFailRight()
    {
        $testStatus = $this->getTestStatus();

        $testId = ProjectTaskStatus::getIdByNameOrFail($testStatus['name']);
        $this->assertEquals($testId, $testStatus['id']);
    }

    /**
     * 不能获取
     */
    public function testGetIdByNameOrFailWrong()
    {
        $this->setExpectedException('Illuminate\Database\Eloquent\ModelNotFoundException');
        ProjectTaskStatus::getIdByNameOrFail('doing');
    }

    /**
     * 获得测试所使用的模型类实例，并填充依赖的数据库
     * @return \Illuminate\Database\Eloquent\Model|static
     */
    private function getTestStatus()
    {
        $this->seed('ProjectTaskStatusTableSeeder');

        return ProjectTaskStatus::firstOrFail();
    }
}