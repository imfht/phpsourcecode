<?php
// +---------------------------------------------------------------------+
// | OneBase    | [ WE CAN DO IT JUST THINK ]                            |
// +---------------------------------------------------------------------+
// | Licensed   | http://www.apache.org/licenses/LICENSE-2.0 )           |
// +---------------------------------------------------------------------+
// | Author     | Bigotry <3162875@qq.com>                               |
// +---------------------------------------------------------------------+
// | Repository | https://gitee.com/Bigotry/OneBase                      |
// +---------------------------------------------------------------------+

namespace app\queue\logic;

use app\common\logic\LogicBase;

/**
 * 逻辑处理
 */
class TestLogic extends LogicBase
{
    
    public function testHandle($data)
    {
        
        // 实际业务流程处理
        
        // sf($data);
        
        return true;
    }
}
