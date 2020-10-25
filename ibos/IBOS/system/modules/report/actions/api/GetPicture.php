<?php
/**
 * 获得模板图标
 */

namespace application\modules\report\actions\api;

use application\core\utils\Ibos;
use application\modules\report\utils\Template;

class GetPicture extends Base
{

    public function run()
    {
        $picture = Template::getPictureName();
        $array = array_keys($picture);
        Ibos::app()->controller->ajaxReturn(array(
            'isSuccess' => true,
            'msg' => '',
            'data' => $array,
        ));
    }
}