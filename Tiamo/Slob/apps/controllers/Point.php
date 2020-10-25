<?php
namespace App\Controller;

use App\BasicController;
use App\Model\City;
use Swoole;

class Point extends BasicController
{

    function index()
    {
        $numPerPage = getRequest('numPerPage', 20, true);
        $pageNum = getRequest('pageNum', 1, true);
        $point = model('Point');
        $params = [
            'order' => 'id',
            'limit' => ($pageNum - 1) * $numPerPage . ',' . $numPerPage
        ];
        $total = $point->count(['where' => 1]);
        $page = [
            'numPerPage' => $numPerPage,
            'pageNum' => $pageNum,
            'total' => $total,
        ];

        $data = $point->gets($params);
        $this->assign('data', $data);
        $this->assign('page', $page);
        $this->display('point/index.php');
    }

    function view()
    {
        $numPerPage = getRequest('numPerPage', 20, true);
        $pageNum = getRequest('pageNum', 1, true);
        $point = model('Point');
        $params = [
            'type' => 'view',
            'order' => 'id',
            'limit' => ($pageNum - 1) * $numPerPage . ',' . $numPerPage
        ];
        $total = $point->count(['where' => 1]);
        $page = [
            'numPerPage' => $numPerPage,
            'pageNum' => $pageNum,
            'total' => $total,
        ];
        $data = $point->getPage($params);
        $this->assign('data', $data);
        $this->assign('page', $page);
        $this->display('point/view.php');
    }

    function food()
    {
        $numPerPage = getRequest('numPerPage', 20, true);
        $pageNum = getRequest('pageNum', 1, true);
        $point = model('Point');
        $params = [
            'type' => 'food',
            'order' => 'id',
            'limit' => ($pageNum - 1) * $numPerPage . ',' . $numPerPage
        ];
        $total = $point->count(['where' => 1]);
        $page = [
            'numPerPage' => $numPerPage,
            'pageNum' => $pageNum,
            'total' => $total,
        ];
        $data = $point->getPage($params);
        $this->assign('data', $data);
        $this->assign('page', $page);
        $this->display('point/food.php');
    }

    function addPoint()
    {
        $type = getRequest('type');
        if (isPost()) {
            $point = model('Point');
            $xModel = model($type);
            $xdata = $xModel->getData();
            $data = $point->getData();
            $data['obj'] = json_encode($xdata);
            $data['ctime'] = time();
            if ($point->create($data)) {
                jsonReturn($this->ajaxFromReturn('添加成功', 200, 'closeCurrent', '', 'point'));
            } else {
                jsonReturn($this->ajaxFromReturn('添加失败', 300));
            }
        }
        $mCity = model('City');
        $citys = $mCity->getCityForSelect();
        $this->assign('citys', $citys);
        $this->assign('title', '添加');
        $this->display("point/add_" . $type . ".php");
    }

    function updatePoint()
    {
        $point = model('Point');
        if (isPost()) {
            $type = getRequest('type');
            $xModel = model($type);
            $data = $point->getData();
            $old = $point->get($data['id'])->get();
            $item = json_decode($old['obj'],1);
            $xdata = $xModel->getData($item);
            $data['obj'] = json_encode($xdata);
            if ($point->set($data['id'], $data)) {
                jsonReturn($this->ajaxFromReturn('修改成功', 200, 'closeCurrent', '', 'point'));
            } else {
                jsonReturn($this->ajaxFromReturn('修改失败', 300));
            }
        }
        $id = getRequest('id');
        $data = $point->get($id)->get();
        $type = $data['type'];
        $xModel = model($type);
        $xdata = $xModel->analyzeData($data['obj']);
        $data = $data + $xdata;
        //var_dump($data);exit;
        $mCity = model('City');
        $citys = $mCity->getCityForSelect();
        $this->assign('citys', $citys);
        $this->assign('data', $data);
        $this->assign('title', '修改');
        $this->display("point/update_" . $type . ".php");
    }

    function deletePoint()
    {
        $id = getRequest('id');
        $point = model('Point');
        if ($point->del($id)) {
            jsonReturn($this->ajaxFromReturn('删除成功', 200, '', '', 'point'));
        } else {
            jsonReturn($this->ajaxFromReturn('删除失败', 300));
        }
    }

    function searchPoint()
    {

    }

}	
