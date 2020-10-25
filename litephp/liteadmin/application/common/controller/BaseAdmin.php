<?php
namespace app\common\controller;

use app\common\middleware\CheckAccess;
use LiteAdmin\Tree;
use think\App;
use think\Controller;
use think\Db;
use think\db\Where;
use think\exception\PDOException;
use think\facade\Cache;
use think\Loader;

/**
 * 后台基础控制器
 * Class BaseAdmin
 * @package app\common\controller
 */
class BaseAdmin extends Controller
{
    protected $middleware = [
        CheckAccess::class
    ];

    /**
     * 初始化
     */
    protected function initialize()
    {

    }

    /**
     * 构造函数
     * BaseAdmin constructor.
     * @param App|null $app
     */
    public function __construct(App $app = null)
    {
        parent::__construct($app);
        // 面包屑数据
        $module = $this->request->module();
        $controller = Loader::parseName($this->request->controller(),0);
        $action = $this->request->action();

        $ctitie = Cache::remember("{$module}/{$controller}",function ()use($module,$controller){
            return Db::name('SystemAuthNode')->where('path',"{$module}/{$controller}")->value('title');
        },600);
        $atitie = Cache::remember("{$module}/{$controller}/{$action}",function () use ($module,$controller,$action){
            return Db::name('SystemAuthNode')->where('path',"{$module}/{$controller}/{$action}")->value('title');
        },600);

        $this->assign(['ctitle'=>$ctitie,'atitle'=>$atitie]);

        // 当前控制器
        $classuri = $this->request->module().'/'.$this->request->controller();
        $this->assign('classuri',$classuri);
    }

    /**
     * 万能列表方法
     * @param $query
     * @param bool $multipage
     * @param array $param
     * @return mixed
     */
    protected function _list($query,$multipage = true,$pageParam = [])
    {
        if ($this->request->isGet()){
            if ($multipage){
//                unset($param['page']);
                $pageResult = $query->paginate(null,false,['query'=>$pageParam]);
                $this->assign('page',$pageResult->render());
                $result = $pageResult->all();
            }else{
                $result = $query->select();
            }
            if (false !== $this->_callback('_list_before', $result, [])) {
                $this->assign('list',$result);
                return $this->fetch();
            }
            return $result;
        }
    }

    /**
     * 表单万能方法
     * @param $query
     * @param string $tpl
     * @param string $pk
     * @param array $where
     * @return array|mixed
     */
    protected function _form($query, $tpl = '', $pk='', $where = []) {
        $pk = $pk?:($query->getPk()?:'id');
        $defaultPkValue = isset($where[$pk])?$where[$pk]:null;
        $pkValue = $this->request->route($pk,$defaultPkValue);

        if ($this->request->isGet()){
            $vo = ($pkValue !== null) ? $query->where($pk,$pkValue)->where($where)->find():[];
            if (false !== $this->_callback('_form_before', $vo)) {
                return $this->fetch($tpl,['vo'=>$vo]);
            }
            return $vo;
        }
        $data = $this->request->post();
        if (false !== $this->_callback('_form_before', $data)) {
            try{
                if (isset($data[$pk])){
                    $where[$pk] = ['=',$data[$pk]];
                    $result = $query->where(new Where($where))->update($data);
                    $last_id = $data[$pk];
                }else{
                    $result = $query->insert($data);
                    $last_id = $query->getLastInsID();
                }
            }catch (PDOException $e){
                $this->error($e->getMessage());
            }
            //手动释放所有查询条件
            $query->removeOption();
            $last_data = $query->find($last_id);
            if (false !== $this->_callback('_form_after',  $last_data)) {
                if ($result !== false) {
                    $this->success('恭喜, 数据保存成功!', '');
                }
                $this->error('数据保存失败, 请稍候再试!');
            }else{
                $this->error("表单后置操作失败，请检查数据！");
            }
        }
    }

    /**
     * @param $ids
     * @throws PDOException
     * @throws \think\Exception
     */
    protected function _del($model,$ids)
    {
        $fields = $model::getTableFields();
        if (in_array('is_deleted',$fields)){
            $res = $model->whereIn('id', $ids)
                ->update(['is_deleted' => 1]);
        }else{
            $res = $model->whereIn('id', $ids)
                ->delete();
        }
        if ($res) {
            $this->success('删除成功！', '');
        } else {
            $this->error("删除失败");
        }
    }

    protected function _change($model,$id,$data)
    {
        $res = $model->where('id', $id)->update($data);
        if ($res) {
            $this->success('切换状态操作成功！');
        } else {
            $this->error('切换状态操作失败！');
        }
    }






    /**
     * 回调唤起
     * @param $method
     * @param $data1
     * @param $data2
     * @return bool
     */
    protected function _callback($method, &$data)
    {
        foreach ([$method, "_" . $this->request->action() . "{$method}"] as $_method) {
            if (method_exists($this, $_method) && false === $this->$_method($data)) {
                return false;
            }
        }
        return true;
    }

}
