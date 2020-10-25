<?php
/**
 * Created by JetBrains PhpStorm.
 * User: taotao
 * Date: 14-6-8
 * Time: 上午11:24
 * To change this template use File | Settings | File Templates.
 */

namespace Admin\Controller;


class AuthController extends BaseController{
    public function index(){
        $Auth = D('AuthGroup'); // 实例化User对象
        $count      = $Auth->count();// 查询满足要求的总记录数
        $Page       = new \Think\Page($count,25);// 实例化分页类 传入总记录数和每页显示的记录数(25)
        $Page->setConfig('prev','上一页');
        $Page->setConfig('next','下一页');
        $Page->setConfig('first','首页');
        $Page->setConfig('end','末页');
        //$Page->setConfig('theme', '<ul class="pagination pagination-sm"><li><a> %HEADER%</a></li> <li><a>%FIRST%</a></li> <li><a>%UP_PAGE%</a></li> <li class="active"><a >%LINK_PAGE%</a></li> <li><a>%DOWN_PAGE% %END% </ul>');
        $Page->setConfig('theme', '<ul class="pagination pagination-sm"></li><li>%UP_PAGE%</li> <li class="active"><a>%LINK_PAGE%</a></li> <li><a>%DOWN_PAGE%</a></li> </ul>');

        $show       = $Page->show();// 分页显示输出
        // 进行分页数据查询 注意limit方法的参数要使用Page类的属性
        $list = $Auth->order('id')->limit($Page->firstRow.','.$Page->listRows)->select();
        //echo $User->getLastSql();exit;
        $this->assign('list',$list);// 赋值数据集
        $this->assign('page',$show);// 赋值分页输出
        $this->display(); // 输出模板
    }
    public function access(){
        $this->display(); // 输出模板
    }
}