<?php
namespace App\Lib\Alioss;
/**
 * Class Config
 *
 * 执行Sample示例所需要的配置，用户在这里配置好Endpoint，AccessId， AccessKey和Sample示例操作的
 * bucket后，便可以直接运行RunAll.php, 运行所有的samples
 */
final class Config
{
    const OSS_ACCESS_ID = 'LTAIw4ChFAgQbpRV';
    const OSS_ACCESS_KEY = 'RXP1wjSy4Q0vZKHGlRJFfbT6oEiH4A';
    const OSS_ENDPOINT = 'http://oss-cn-shanghai.aliyuncs.com';
    //const OSS_ENDPOINT = 'http://static.buketech.com';
    const OSS_TEST_BUCKET = 'buketech';

}
