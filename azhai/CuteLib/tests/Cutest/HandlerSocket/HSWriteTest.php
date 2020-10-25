<?php
namespace Cutest\HandlerSocket;


class HSWriteTest extends HSFixture
{
    /**
     * @dataProvider provider
     */
    public function test01Insert($term_id, $name, $slug, $term_group)
    {
        $names = explode(',', $GLOBALS['DB_FIELDS']);
        $row = func_get_args();
        $data = array_combine($names, $row);
        $success = self::$hs->insert($data);
        $term = self::$hs->get($term_id);
        /*var_dump($success);
        var_dump($data);
        var_dump($term_id);
        var_dump($term);
        exit;*/
        $this->assertTrue($success);
        $this->assertEquals($name, $term['name']);
    }

    public function test02Update()
    {
        $this->insertRows();
        self::$hs->update([5, 'Apache', 'apache', 1], null, 5);
        $apache = self::$hs->get(5);
        $this->assertEquals('Apache', $apache['name']);
        $this->assertEquals(1, intval($apache['term_group']));
    }

    /**
     * @depends test02Update
     */
    public function test03Delete()
    {
        $this->insertRows();
        self::$hs->delete(6);
        self::$hs->delete(7);
        self::$hs->delete(8);
        $terms = self::$hs->all(null, '>', 2, 3, 1);
        $this->assertEquals(2, count($terms));
        $this->assertEquals('Apache', $terms[1]['name']);
        $this->assertEquals(1, intval($terms[1]['term_group']));
    }
}

