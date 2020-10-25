<?php
/**
 * Created by PhpStorm.
 * User: sheldon
 * Date: 18-6-12
 * Time: 上午10:00.
 */
use MiotApi\Util\Request;

class RequestTest extends PHPUnit_Framework_TestCase
{
    private static $http;

    public static function setUpBeforeClass()
    {
        self::$http = new Request('api.github.com');
    }

    public static function tearDownAfterClass()
    {
        self::$http = null;
    }

    public function testSetType()
    {
        self::$http->setType('GET');
        $this->assertAttributeEquals('GET', 'type', self::$http);

        return self::$http;
    }

    /**
     * @depends testSetType
     */
    public function testSetHost()
    {
        self::$http->setHost('api.github.com');
        $this->assertAttributeEquals('api.github.com', 'host', self::$http);

        return self::$http;
    }

    /**
     * @depends testSetHost
     */
    public function testSetPort()
    {
        self::$http->setPort(443);
        $this->assertAttributeEquals(443, 'port', self::$http);

        return self::$http;
    }

    /**
     * @depends testSetPort
     */
    public function testSetRequestURI()
    {
        self::$http->setRequestURI('/search/repositories');
        $this->assertAttributeEquals('/search/repositories', 'uri', self::$http);

        return self::$http;
    }

    public function testSetTimeout()
    {
        self::$http->setTimeout(10);
        $this->assertAttributeEquals(10, 'timeout', self::$http);

        return self::$http;
    }

    /**
     * @depends testSetRequestURI
     */
    public function testSetQueryParams()
    {
        $params = [
            'q'    => 'language:php',
            'sort' => 'stars',
        ];
        self::$http->setQueryParams($params);
        $this->assertAttributeEquals($params, 'query', self::$http);

        return self::$http;
    }

    /**
     * @depends testSetQueryParams
     */
    public function testSetUseCurl()
    {
        self::$http->setUseCurl(true, true);
        $this->assertAttributeEquals(true, 'useCurl', self::$http);

        return self::$http;
    }

    public function testSetData()
    {
    }

    public function testParam()
    {
    }

    public function testSetUrl()
    {
    }

    public function testSetHeader()
    {
    }

    public function testSetAdditionalCurlOpt()
    {
    }

    public function testSetUseBasicAuth()
    {
    }

    public function testSetAuthUsername()
    {
    }

    public function testSetAuthPassword()
    {
    }

    /**
     * @depends testSetUseCurl
     */
    public function testExecute()
    {
        self::$http->execute();
        $this->assertAttributeEquals(true, 'executed', self::$http);

        return self::$http;
    }

    /**
     * @depends testExecute
     */
    public function testGetError()
    {
    }

    /**
     * @depends testExecute
     */
    public function testGetResponseText()
    {
        $this->assertContains('incomplete_results', self::$http->GetResponseText());

        return self::$http->GetResponseText();
    }

    /**
     * @depends testExecute
     */
    public function testGetResponseHeader()
    {
        $this->assertEquals('200', self::$http->getResponseHeader('http_code'));
    }

    public function testCurlHeaders()
    {
    }

    /**
     * @depends testExecute
     */
    public function testGetResponse()
    {
        $this->assertArrayHasKey('responseText', self::$http->getResponse());

        return self::$http->getResponse();
    }

    /**
     * @depends testExecute
     */
    public function testGetAllResponseHeaders()
    {
        $this->assertArrayHasKey('http_code', self::$http->getAllResponseHeaders());

        return self::$http->getAllResponseHeaders();
    }

    /**
     * @depends testExecute
     */
    public function testClose()
    {
        $this->assertEquals(null, self::$http->close());
    }
}
