<?php


namespace App\Message;


class BuildApiUpdateLog
{
    private $info_id;

    public function __construct(string $info_id)
    {
        $this->info_id = $info_id;
    }

    public function getInfoId(): string
    {
        return $this->info_id;
    }
}