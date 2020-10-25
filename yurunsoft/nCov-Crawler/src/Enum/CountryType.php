<?php
namespace ImiApp\Enum;

use Imi\Enum\BaseEnum;
use Imi\Enum\Annotation\EnumItem;

abstract class CountryType extends BaseEnum
{
    /**
     * @EnumItem("中国")
     */
    const CHINA = 1;

    /**
     * @EnumItem("外国")
     */
    const FOREIGN = 2;

}
