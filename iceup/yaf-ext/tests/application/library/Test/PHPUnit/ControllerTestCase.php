<?php

namespace Test\PHPUnit;

require_once APPLICATION_PATH . '/tests/application/library/Test/PHPUnit/TestCase.php';

class ControllerTestCase extends \Test\PHPUnit\TestCase {

    protected function _dispatch($request) {
        try {
            $response = $this->getApplication()->getDispatcher()
                    ->catchException(false)
                    ->returnResponse(true)
                    ->dispatch($request);
            $content  = $response->getBody();
        } catch (Exception $exc) {
            $content = json_encode(array('errno' => $exc->getCode()));
        }

        return json_decode($content, true);
    }

    protected function _test($listTestData) {
        foreach ($listTestData as $testData) {
            if (isset($testData['cookie'])) {
                $_COOKIE = $testData['cookie'];
            }
            if (isset($testData['post'])) {
                $_POST = $testData['post'];
            }
            if (isset($testData['get'])) {
                $_GET = $testData['get'];
            }
            $request = new \Yaf\Request\Simple("CLI", $testData['request'][0], $testData['request'][1], $testData['request'][2], $_GET);
            $data    = $this->_dispatch($request);
            $this->assertSame($testData['code'], $data['errno']);
            if (isset($testData['data'])) {
                $this->assertEquals($testData['data'], $data['data']);
            }
        }
    }

}
