<?php
/**
 * Created by PhpStorm.
 * User: spatra
 * Date: 14-11-21
 * Time: 下午10:59
 */

/**
 * Class BaseControllerTest
 *
 * 用于对BaseController中扩展的自定义方法进行测试.
 */
class BaseControllerTest extends TestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->controllerObj = $this->app->make('BaseController');
    }

    /**
     * 测试方法：getSectionalValuesFromModel
     */
    public function testGetSectionalValuesFromModel()
    {
        Artisan::call('migrate');
        $this->seed('UserTableTestSeeder');
        $this->seed('ProjectTableTestSeeder');

        $testProject = Project::firstOrFail();

        $keys = ['name', 'introduction', 'creater'];
        $resp = $this->controllerObj->getSectionalValuesFromModel($testProject, $keys);
        $this->arrayMustHasKeys($resp, $keys, true);

        $keys = ['username', 'email'];
        $this->arrayMustHasKeys($resp['creater'], $keys, true);

    }

    /**
     * 测试方法：changeValidatorMessageToString
     */
    public function testChangeValidatorMessageToString()
    {
        $data = [
            'username'=>null,
            'email'=>'sq.com'
        ];

        $rules = [
            'username'=>'required|alpha_dash',
            'email'=>'email'
        ];

        $validator = Validator::make($data, $rules);
        $this->assertFalse($validator->passes());

        $keys = ['username', 'email'];
        $separator = ',';
        $result = $this->controllerObj->changeValidatorMessageToString( $validator->messages(), $keys, $separator );
        $this->assertTrue( is_string($result) );
        $this->assertEquals(count($data), count( explode($separator, $result)) );

        $result = $this->controllerObj->changeValidatorMessageToString( $validator->messages() );
        $this->assertTrue( is_string($result) );

    }

    private $controllerObj;
}