<?php
namespace App\Controller;

use App\BasicController;
use Swoole;

class City extends BasicController
{

    function index()
    {
        $numPerPage = getRequest('numPerPage', 20, true);
        $pageNum = getRequest('pageNum', 1, true);
        $city = model('City');
        $params = [
            'order' => 'id',
            'limit' => ($pageNum - 1) * $numPerPage . ',' . $numPerPage
        ];
        $total = $city->count(['where' => 1]);
        $page = [
            'numPerPage' => $numPerPage,
            'pageNum' => $pageNum,
            'total' => $total,
        ];

        $data = $city->getPage($params);
        $this->assign('data', $data);
        $this->assign('page', $page);
        $this->display('city/index.php');
    }

    function addCity()
    {
        if (isPost()) {
            $city = model('City');
            $data = $city->getData();
            $data['ctime'] = time();
            if ($city->create($data)) {
                jsonReturn($this->ajaxFromReturn('添加成功', 200, 'closeCurrent', '', 'city'));
            } else {
                jsonReturn($this->ajaxFromReturn('添加失败', 300));
            }
        }
        $this->assign('title', '添加');
        $this->display("city/add_city.php");
    }

    function updateCity()
    {
        $city = model('City');
        if (isPost()) {
            $id = getRequest('id');
            $item = $city->get($id);
            $data = $city->getData($item);
            $data['ctime'] = time();
            if ($city->set($data['id'], $data)) {
                jsonReturn($this->ajaxFromReturn('修改成功', 200, 'closeCurrent', '', 'city'));
            } else {
                jsonReturn($this->ajaxFromReturn('修改失败', 300));
            }
        }
        $id = getRequest('id');
        $data = $city->get($id)->get();
        $data['imgs'] = json_decode($data['imgs'], 1);
        foreach ($data['imgs'] as &$img) {
            if (strpos($img, WEBROOT) === false) {
                $img = WEBROOT . "/local/" . $img;
            }
        }
        $this->assign('data', $data);
        $this->assign('title', '修改');
        $this->display("city/update_city.php");
    }

    function deleteCity()
    {
        $id = getRequest('id');
        $city = model('City');
        if ($city->del($id)) {
            jsonReturn($this->ajaxFromReturn('删除成功', 200, '', '', 'city'));
        } else {
            jsonReturn($this->ajaxFromReturn('删除失败', 300));
        }
    }

    function searchCity()
    {

    }

}	
