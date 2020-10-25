<?php declare(strict_types = 1);
namespace msqphp\test\base\str;

class StrTest extends \msqphp\test\Test
{
    public function testStart() : void
    {
        $this->class('\msqphp\base\str\Str');
        $this->testThis();
    }
    public function testRandomString() : void
    {
        $len = rand(1, 1000);
        $rand = rand(1, 7);
        $preg = [
            '',
            '/^[0-9]{'.$len.'}$/',
            '/^[0-9a-z]{'.$len.'}$/',
            '/^[0-9a-zA-Z]{'.$len.'}$/',
            '/^[0-9a-zA-Z\~\`\!\@\#\$\%\^\&\*\(\)\_\+\-\=\[\]\{\}\:\|\<\>\?\, \.\/\\\\\;\'\"]{'.$len.'}$/',
            '/^[a-zA-Z]{'.$len.'}$/',
            '/^[a-zA-Z\-\_]{'.$len.'}$/',
            '/^[a-zA-Z\~\`\!\@\#\$\%\^\&\*\(\)\_\+\-\=\[\]\{\}\:\|\<\>\?\, \.\/\;\'\"\\\\]{'.$len.'}$/',
        ];
        $this->clear()->method('randomString')->args($len, $rand)->result(
            function($result) use ($preg, $rand) {
                return false !== preg_match($preg[$rand], $result);
            }
        )->test();
    }
    public function testRandomBytes() : void
    {
        $len = rand(1, 1000);
        $this->clear()->method('randomBytes')->args($len)->result(
            function($result) use ($len) {
                return strlen($result) === $len;
            }
        )->test();
    }
    public function testRandom() : void
    {
        $len = rand(1, 1000);
        $preg = '/^[0-9a-zA-Z]{'.$len.'}$/';
        $this->clear()->method('random')->args($len)->result(
            function($result) use ($preg) {
                return false !== preg_match($preg, $result);
            }
        )->test();
    }
    public function testQuickRandom() : void
    {
        $len = rand(1, 1000);
        $preg = '/^[0-9a-zA-Z]{'.$len.'}$/';
        $this->clear()->method('quickRandom')->args($len)->result(
            function($result) use ($preg) {
                return false !== preg_match($preg, $result);
            }
        )->test();
    }
    public function testContains() : void
    {
        $string = 'hello world this is a test';
        $this->clear()->method('contains');
        $this->args($string, 'hello')->result(true)->test();
        $this->args($string, ['nihao', 'is'])->result(true)->test();
        $this->args($string, 'buhao')->result(false)->test();
        $this->args($string, 'a')->result(true)->test();
        $this->args($string, 'test')->result(true)->test();

    }
    public function testStartsWith() : void
    {
        $this->clear()->method('startsWith');
        $this->args('test 1', 'test')->result(true)->test();
        $this->args('test 1', 'atest')->result(false)->test();
        $this->args('nna', ['a', 'nna'])->result(true)->test();
    }
    public function testEndsWith() : void
    {
        $this->clear()->method('endsWith');
        $this->args('test 1', '1')->result(true)->test();
        $this->args('test 1', '0')->result(false)->test();
        $this->args('nna', ['a', 'nna'])->result(true)->test();
    }
}