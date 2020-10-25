<?php


namespace App\Message;


class BuildDocumentRedisData
{
    private $info_id;

    private $show;

    public function __construct(string $info_id, $show = false)
    {
        $this->info_id = $info_id;
        $this->show = $show;
    }

    public function getInfoId(): string
    {
        return $this->info_id;
    }

    public function getShow(): bool
    {
        return $this->show;
    }
}