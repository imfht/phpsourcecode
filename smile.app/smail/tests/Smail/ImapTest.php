<?php
use Smail\Imap;

class ImapTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @test
     */
    public function getMailBox()
    {
        $imap = new Imap();
        $stream = $imap->login('spiderman1517650@sina.com', '15176501024btx');
        $box_list = $imap->mailbox_list($stream);
        var_export($box_list);
    }
}