<?php

/**
 * Breadcrumb.php
 *
 * @copyright  2017 opencart.cn - All Rights Reserved
 * @link       http://www.guangdawangluo.com
 * @author     Sam Chen <samchen@opencart.cn>
 * @created    2017-12-25 10:18
 * @modified   2017-12-25 10:18
 */
class Breadcrumb
{
    private $data = array();

    public function addHome()
    {
        $this->add(t('text_home'), url()->link('common/home'));
    }

    public function add($text, $href)
    {
        if (empty($text) || empty($href)) {
            return;
        }
        $this->data[] = array(
            'text' => $text,
            'href' => $href
        );
    }

    public function all()
    {
        return $this->data;
    }
}
