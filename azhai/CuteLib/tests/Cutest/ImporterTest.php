<?php
namespace Cutest;

use \PHPUnit_Framework_TestCase as TestCase;
use \Cute\Importer;


class ImporterTest extends TestCase
{
    public $importer;

    public function setUp()
    {
        $this->importer = Importer::getInstance();
    }

    public function test01AddNamespace()
    {
        $this->importer->addNamespace('Twig', VENDOR_ROOT . '/Twig/lib');
        $lower = new \Twig_SimpleFunction('lower', 'strtolower');
        $this->assertInstanceOf('\\Twig_SimpleFunction', $lower);
    }

    public function test02AddClass()
    {
        $dir = VENDOR_ROOT . '/Gregwar/Captcha';
        $this->importer->addClass($dir . '/PhraseBuilder.php',
            '\\Gregwar\\Captcha\\PhraseBuilder');
        $this->importer->addClass($dir . '/PhraseBuilderInterface.php',
            '\\Gregwar\\Captcha\\PhraseBuilderInterface');
        $builder = new \Gregwar\Captcha\PhraseBuilder();
        $phrase = $builder->build(7);
        $this->assertEquals(strlen($phrase), 7);
    }
}

