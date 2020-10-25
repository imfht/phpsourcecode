<?php declare(strict_types = 1);
namespace msqphp\main\view\component;

use msqphp\core;

final class Group
{
    private $group = '';

    public function __construct()
    {
        // 获取分组信息
        $group_info = core\route\Route::getGroupInfo();
        $group = '';

        for ($i = 0, $l = count($group_info) / 2; $i < $l; ++$i) {
            $group .=  $group_info[$i] . DIRECTORY_SEPARATOR;
        }

        $this->group = $group;
    }

    public function get() : string
    {
        return $this->group;
    }

    public function set(string $group) : void
    {
        $this->group = $group;
    }
}