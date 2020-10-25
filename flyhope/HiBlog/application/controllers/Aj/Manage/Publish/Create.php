<?php
/**
 * 新建发布
 *
 * @package Controller
 * @author chengxuan <i@chengxuan.li>
 */
class Aj_Manage_Publish_CreateController extends Aj_AbsController {
    
    
    public function indexAction() {
        $publishs = Comm\Arg::post('publish', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
        
        $datas = array();
        foreach($publishs as $publish) {
            $publis_arr = explode('-', $publish);
            if(isset($publis_arr[1]) && ctype_digit($publis_arr[0])&& ctype_digit($publis_arr[1])) {
                $type = $publis_arr[0];
                $connection_id = $publis_arr[1];
            }
            
            $datas[] = array(
                'type'       => $type,
                'connection_id' => $connection_id,
            );
        }
        
        $result = Model\Publish\Task::createBatch($datas);
        
        $msg = _('添加发布任务成功');
        Comm\Response::json(100000, $msg, ['result' => $result], false);
    }
    
}
