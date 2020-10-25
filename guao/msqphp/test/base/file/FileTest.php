<?php declare(strict_types = 1);
namespace msqphp\test\base\file;

class FileTest extends \msqphp\test\Test
{
    public $file = null;
    public $path = '';

    public function testStart() : void
    {
        $this->path = __DIR__.DIRECTORY_SEPARATOR.'Resource'.DIRECTORY_SEPARATOR;
        $this->testThis($this);
    }
    public function testFile1() : void
    {
    }
}