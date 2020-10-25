<?php
namespace Cutest\HandlerSocket;


class HSReadTest extends HSFixture
{

    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();
        self::insertRows();
    }

    public function test01Connect()
    {
        $this->assertInstanceOf('\\Cute\\ORM\\HandlerSocket', self::$hs);
        $this->assertNotNull(self::$hs);
    }

    public function test02GetByID()
    {
        $nginx = self::$hs->get(5);
        $this->assertEquals('Nginx', $nginx['name']);
        $this->assertEquals(0, intval($nginx['term_group']));
    }

    public function test03GetByName()
    {
        $php = self::$hs->get('name', 'PHP语言');
        $this->assertEquals('PHP语言', $php['name']);
        $this->assertEquals(1, intval($php['term_group']));
    }

    public function test04Find()
    {
        $terms = self::$hs->all(null, '>=', 1, 3, 1);
        $this->assertEquals(3, count($terms));
        $this->assertEquals('Python', $terms[1]['name']);
        $this->assertEquals(0, intval($terms[1]['term_group']));
    }

    public function test05In()
    {
        $terms = self::$hs->in('name', 'Nginx', 'MySQL');
        $this->assertEquals(2, count($terms));
        $this->assertEquals('MySQL', $terms[1]['name']);
        $this->assertEquals(1, intval($terms[1]['term_group']));
    }
}

