<?php
use Smail\Smtp;
class SmtpTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @test
     */
    public function smtp()
    {
        $smtp = new Smtp('spiderman1517650@sina.com', '15176501024btx');
        $rst = $smtp->send_message('18710021649@163.com', '18710021649@163.com', '18710021649@163.com', 1, 'hello', 'hello');
        $this->assertNotEmpty($rst);
    }
}