<?php
// +----------------------------------------------------------------------
// | UCToo [ Universal Convergence Technology ]
// +----------------------------------------------------------------------
// | Copyright (c) 2014-2017 http://uctoo.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: Patrick <contact@uctoo.com>
// +----------------------------------------------------------------------


namespace Mpbase\Controller;

use  app\admin\builder\AdminConfigBuilder;
use think\Controller;
/**
 * 前台业务逻辑都放在
 * @var
 */

class Index extends Controller
{

    public function _initialize()
    {

    }

    public function index($page = 1, $mp_id = 0)
    {

        return $this->fetch();
    }

}