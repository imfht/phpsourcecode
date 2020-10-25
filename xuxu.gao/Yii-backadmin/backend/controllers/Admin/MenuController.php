<?php
/**
 * Created by PhpStorm.
 * User: xu.gao
 * Date: 2016/1/20
 * Time: 9:16
 */

namespace backend\controllers\Admin;


use backend\controllers\BaseController;
use backend\tools\Flush;
use backend\tools\ResponseUtils;
use Yii;
use yii\web\Response;

class MenuController extends BaseController{


    private $menuService;
    private $permissionService;
    public function init(){

        $this->menuService       = Yii::createObject('menuservice');
        $this->permissionService = Yii::createObject('permissionservice');
    }
    /**
     * 添加菜单
     */
    public function actionMenuadd(){

        if(Yii::$app->request->isGet)
        {
            //查询父级菜单
            $menus = $this->menuService->queryMenus(['parent_id'=>'0']);
            //查询权限列表
            $permissionList = $this->permissionService->queryAllPermission(['type'=>2]);

            return $this->render('menuadd',['menus' => $menus,'permissionList'=>$permissionList]);
        }elseif(Yii::$app->request->isPost){

            $menus = $this->menuService->queryMenus(['parent_id'=>'0']);

                $model = $this->menuService->menuAdd(Yii::$app->request->post());
                if(is_object($model)){

                    //查询权限列表
                    $permissionList = $this->permissionService->queryAllPermission(['type'=>2]);
                    return $this->render('menuadd',['model'=>$model,'error'=>$model->errors,'menus'=>$menus,'permissionList'=>$permissionList]);
                }else{
                    $menus = $this->menuService->queryMenus(['parent_id'=>'0']);
                    Flush::success('添加成功');
                    return $this->render('menuadd',['menus'=>$menus]);
                }
        }
    }

    /**
     * 菜单列表
     */
    public function actionMenulist(){

        $request = Yii::$app->request;
        if($request->isAjax){

            $params['search']   = $request->post('searchPhrase','');
            $sort               = $request->post('sort');
            $params['sort']     = key($sort).' '.$sort[key($sort)];
            $params['pageIndex']= $request->post('current',1);
            $params['pageSize'] = $request->post('rowCount',10);
            $data               = $this->menuService->menuList($params);
            $totalCount         = $this->menuService->menuCount($params);
            $json_data = array(
                "current"        => intval( $params['pageIndex'] ),
                "rowCount"       => intval( $params['pageSize'] ),
                "total"          => intval( $totalCount ),
                "rows"           => $data
            );
            Yii::$app->response->format = Response::FORMAT_JSON;
            return $json_data;
        }
        return $this->render('menulist');
    }
    /**
     * 获取菜单的子数据
     */
    public function actionMenuchild(){

        $id     = Yii::$app->request->get('id',0);
        $list   = $this->menuService->queryMenus(['parent_id'=>$id]);
        Yii::$app->response->format = Response::FORMAT_JSON;
        return $list;
    }
    /**
     * 更新菜单
     */
    public function actionMenuupdate($id){

        if(Yii::$app->request->isGet)
        {
            //查询父级菜单
            $menus = $this->menuService->queryMenus(['parent_id'=>'0']);
            //查询当前的菜单信息
            $menu = $this->menuService->menuById($id);
            //查询权限列表
            $permissionList = $this->permissionService->queryAllPermission(['type'=>2]);
            return $this->render('menuupdate',['menus' => $menus,'model'=>$menu,'permissionList'=>$permissionList]);

        }elseif(Yii::$app->request->isPost){

            $model   = $this->menuService->menuUpdate(Yii::$app->request->post());
            if($model->errors){

                //查询父级菜单
                $menus = $this->menuService->queryMenus(['parent_id'=>'0']);
                //查询当前的菜单信息
                $menu = $this->menuService->menuById(Yii::$app->request->post('id'));
                //查询权限列表
                $permissionList = $this->permissionService->queryAllPermission(['type'=>2]);
                return $this->render('menuupdate',['menus' => $menus,'model'=>$menu,'permissionList'=>$permissionList]);

            }else{

                return $this->redirect('/Admin/menu/menulist');
            }
        }
    }
    /**
     * 删除菜单
     */
    public function actionMenudelete(){


        $ret = $this->menuService->menuDelete(Yii::$app->request->post('id',0));
        Yii::$app->response->format = Response::FORMAT_JSON;
        return ResponseUtils::response_data($ret,'删除');
    }
    public function actionTest()
    {
        return $this->render('test');
    }
    public function actionTestAdd()
    {
        $this->layout = false;
        return $this->render('testadd',['name' =>'sssssss']);
    }
}