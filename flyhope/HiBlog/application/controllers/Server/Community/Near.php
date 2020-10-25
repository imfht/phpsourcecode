<?php
/**
 * 附近的极客
 *
 * @package Controller
 * @author  chengxuan <i@chengxuan.li>
 */
class Server_Community_NearController extends AbsController {

    /**
     * 不允许未登录访问
     *
     * @var boolean
     */
    protected $_need_login = true;

    public function indexAction() {
        $result = Model\Near::showNear(1, 12);
        
        $user = new Api\Github\Users();

        foreach($result->near as $key => $value) {
            /**
             * @todo 优化为批量处理
             */
            $result->near[$key]['user'] = $user->show($value['login']);
            $location = trim($value['location_str'], 'POINT() ');
            $location = explode(' ', $location);
            $distance = Comm\Location::distance($result->location->content->point->x, $result->location->content->point->y, $location[0], $location[1]);
            $result->near[$key]['distance'] = sprintf('%.2f 公里', $distance);
        }
        
        $this->viewDisplay(array(
            'result' => $result,
        ));
        
    }
}
