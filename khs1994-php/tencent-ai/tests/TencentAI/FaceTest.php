<?php

declare(strict_types=1);

namespace TencentAI\Tests;

use TencentAI\Exception\TencentAIException;

class FaceTest extends TencentAITestCase
{
    const IMAGE = __DIR__.'/../resource/face/';

    const OUTPUT = __DIR__.'/../output/face/';

    const IMAGE1 = self::IMAGE.'wxc.jpg';

    const IMAGE2 = self::IMAGE.'wxc2.jpg';

    const IMAGE3 = self::IMAGE.'wxc3.jpg';

    const IMAGE5 = self::IMAGE.'wxc5.jpg';

    const DETECT_CROSS_IMAGE_1 = self::IMAGE.'peterye1.jpg';

    const DETECT_CROSS_IMAGE_2 = self::IMAGE.'peterye2.jpg';

    const PERSON_ID = 'testPersonId';

    const PERSON_NAME = 'testPersonName';

    const PERSON_TAG = 'testPersonTag';

    private $name;

    private $array;

    private function face()
    {
        return $this->ai()->face();
    }

    /**
     * @throws TencentAIException
     */
    public function DeleteForce(): void
    {
        $this->array = $this->face()->deletePerson(self::PERSON_ID);
    }

    /**
     * 人体创建.
     *
     * @throws TencentAIException
     * @throws \Exception
     *
     * @return mixed
     */
    public function testCreatePerson()
    {
        $this->DeleteForce();

        $this->name = __FUNCTION__;

        // 单个组ID
        $this->array = $array = $this
            ->face()
            ->createPerson(['test'], self::PERSON_ID, self::PERSON_NAME, self::IMAGE1, self::PERSON_TAG);
        $this->face()->deletePerson(self::PERSON_ID);

        // 多个组ID为
        $this->array = $array = $this
            ->face()
            ->createPerson(['test1', 'test2'], self::PERSON_ID, self::PERSON_NAME, self::IMAGE1, self::PERSON_TAG);
        $this->assertEquals(0, $array['ret']);

        return $faceId = $array['data']['face_id'];
    }

    /**
     * 获取人体列表.
     *
     * @depends testCreatePerson
     *
     * @throws TencentAIException
     */
    public function testGetPersonList(): void
    {
        $this->name = __FUNCTION__;

        $this->array = $this->face()->getPersonList('test1');

        // $this->assertContains('ok', $array['msg']);
        // $this->assertJsonStringEqualsJsonString('0', json_encode($array['ret']));
    }

    /**
     * 获取组列表.
     *
     * @depends testCreatePerson
     *
     * @throws TencentAIException
     */
    public function testGetGroupList(): void
    {
        $this->name = __FUNCTION__;

        $this->array = $this->face()->getGroupList();
    }

    /**
     * 个体 => 增加人脸.
     *
     * @depends testCreatePerson
     *
     * @throws TencentAIException
     * @throws \Exception
     */
    public function testAdd()
    {
        $this->name = __FUNCTION__;

        $this->array = $array = $this->face()->add(self::PERSON_ID, [self::IMAGE2], self::PERSON_TAG);
        $this->assertEquals(0, $array['ret']);
        $this->array = $array = $this->face()->add(self::PERSON_ID, [self::IMAGE3, self::IMAGE5], self::PERSON_TAG);
        $this->assertEquals(0, $array['ret']);

        return $faceIds = $array['data']['face_ids'];
    }

    /**
     * 个体 => 获取人脸列表.
     *
     * @depends testCreatePerson
     *
     * @throws TencentAIException
     */
    public function testGetList(): void
    {
        $this->name = __FUNCTION__;

        $this->array = $this->face()->getList(self::PERSON_ID);
    }

    /**
     * 获取人脸信息.
     *
     * @param string $faceId
     *
     * @depends testCreatePerson
     *
     * @throws TencentAIException
     */
    public function testGetInfo(string $faceId): void
    {
        $this->name = __FUNCTION__;

        $this->array = $this->face()->getInfo($faceId);
    }

    /**
     * 个体 => 删除人脸.
     *
     * @param array $faceIds
     *
     * @depends testAdd
     *
     * @throws TencentAIException
     */
    public function testDelete(array $faceIds): void
    {
        $this->name = __FUNCTION__;

        $this->array = $this->face()->delete('testPersonId', $faceIds);
    }

    /**
     * 设置个体信息.
     *
     * @depends testCreatePerson
     *
     * @throws TencentAIException
     */
    public function testSetPersonInfo(): void
    {
        $this->name = __FUNCTION__;

        $this->array = $this->face()->setPersonInfo(self::PERSON_ID, 'testPersonNewName', 'testPersonNewTag');
    }

    /**
     * 获取个体信息.
     *
     * @depends testCreatePerson
     *
     * @throws TencentAIException
     */
    public function testGetPersonInfo(): void
    {
        $this->name = __FUNCTION__;

        $this->array = $this->face()->getPersonInfo(self::PERSON_ID);
    }

    /**
     * 人脸分析.
     *
     * @throws TencentAIException
     */
    public function testDetect(): void
    {
        $this->name = __FUNCTION__;

        $this->array = $this->face()->detect(self::IMAGE1);
    }

    /**
     * 多人脸识别.
     *
     * @throws TencentAIException
     */
    public function testMultiDetect(): void
    {
        $this->name = __FUNCTION__;

        $this->array = $this->face()->multiDetect(self::IMAGE1);
    }

    /**
     *五官检测.
     *
     * @throws TencentAIException
     */
    public function testShape(): void
    {
        $this->name = __FUNCTION__;

        $this->array = $this->face()->shape(self::IMAGE1, false);
    }

    /**
     * 如果一个测试函数添加了 @test 注解，那么测试函数名字就不必以 test 开头.
     *
     * 人脸对比
     *
     * @test
     *
     * @throws TencentAIException
     */
    public function compare(): void
    {
        $this->name = __FUNCTION__;

        $this->array = $this->face()->compare([self::IMAGE1, self::IMAGE3]);
    }

    /**
     * 人脸识别.
     *
     * @depends testCreatePerson
     *
     * @throws TencentAIException
     */
    public function testIdentify(): void
    {
        $this->name = __FUNCTION__;

        $this->array = $this->face()->identify('test1', self::IMAGE3, 9);
    }

    /**
     * 跨年龄人脸识别.
     *
     * @throws TencentAIException
     */
    public function testDetectCrossAge(): void
    {
        $this->name = __FUNCTION__;

        $this->array = $this->face()->detectCrossAge(self::DETECT_CROSS_IMAGE_1, self::DETECT_CROSS_IMAGE_2);
    }

    /**
     * 人脸验证
     *
     * @depends testCreatePerson
     *
     * @throws TencentAIException
     */
    public function testVerify(): void
    {
        $this->name = __FUNCTION__;

        $this->array = $this->face()->verify(self::PERSON_ID, self::IMAGE3);
    }

    /**
     * 删除个体.
     *
     * @depends testCreatePerson
     *
     * @throws TencentAIException
     */
    public function testDeletePerson(): void
    {
        $this->name = __FUNCTION__;

        $this->array = $this->face()->deletePerson(self::PERSON_ID);
    }

    public function tearDown(): void
    {
        $this->assertEquals(0, $this->array['ret']);

        file_put_contents(self::OUTPUT.$this->name.'.json', json_encode($this->array, JSON_UNESCAPED_UNICODE));
    }
}
