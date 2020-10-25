<?php


namespace WxSDK\core\model\card\send;


class Card4LandingPage
{
    /**
     * @var string 所要在页面投放的card_id
     */
    public $card_id;
    /**
     * @var string 缩略图url
     */
    public $thumb_url;

    /**
     * Card4LandingPage constructor.
     * @param string $card_id
     * @param string $thumb_url
     */
    public function __construct(string $card_id, string $thumb_url)
    {
        $this->card_id = $card_id;
        $this->thumb_url = $thumb_url;
    }

}