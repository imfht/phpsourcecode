<?php

namespace app\common\model;
use think\facade\Db;


/**
 * ============================================================================
 * DSMall多用户商城
 * ============================================================================
 * 版权所有 2014-2028 长沙德尚网络科技有限公司，并保留所有权利。
 * 网站地址: http://www.csdeshang.com
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和使用 .
 * 不允许对程序代码以任何形式任何目的的再发布。
 * ============================================================================
 * 数据层模型
 */
class EditablePage extends BaseModel {

    public $page_info;

    /**
     * 新增可编辑页面
     * @author csdeshang
     * @param array $data 参数内容
     * @return bool 布尔类型的返回结果
     */
    public function addEditablePage($data) {
        return Db::name('editable_page')->insertGetId($data);
    }

    /**
     * 删除一个可编辑页面
     * @author csdeshang
     * @param array $editable_page_id 可编辑页面id
     * @return bool 布尔类型的返回结果
     */
    public function delEditablePage($editable_page_id) {
        //删除配置
        model('editable_page_config')->delEditablePageConfig(array('editable_page_id'=>$editable_page_id));
        return Db::name('editable_page')->where('editable_page_id', $editable_page_id)->delete();
    }

    /**
     * 获取可编辑页面列表
     * @author csdeshang
     * @param array $condition 查询条件
     * @param obj $pagesize 分页页数
     * @param str $orderby 排序
     * @return array 二维数组
     */
    public function getEditablePageList($condition = array(), $pagesize = '', $orderby = 'editable_page_id desc') {
        if ($pagesize) {
            $result = Db::name('editable_page')->where($condition)->order($orderby)->paginate(['list_rows'=>$pagesize,'query' => request()->param()],false);
            $this->page_info = $result;
            return $result->items();
        } else {
            return Db::name('editable_page')->where($condition)->order($orderby)->select()->toArray();
        }
    }
    public function getOneEditablePage($condition = array()) {
        return Db::name('editable_page')->where($condition)->find();
    }
    /**
     * 更新可编辑页面记录
     * @author csdeshang
     * @param array $data 更新内容
     * @return bool
     */
    public function editEditablePage($condition, $data) {
        return Db::name('editable_page')->where($condition)->update($data);
    }


    
    public function getEditablePageConfigByPageId($editable_page_id,$store_id=0) {
        $editable_page_config_model=model('editable_page_config');
        $editable_page_config_model->store_id=$store_id;
        $data=array();
        $editable_page_config_list = $editable_page_config_model->getEditablePageConfigList(array('editable_page_id' => $editable_page_id));
        foreach ($editable_page_config_list as $key => $val) {
            $editable_page_config_list[$key]['editable_page_config_content'] = json_decode($val['editable_page_config_content'], true);
            if(isset($editable_page_config_list[$key]['editable_page_config_content']['goods'])){
                $editable_page_config_list[$key]['goods_list']=array();
                
                foreach($editable_page_config_list[$key]['editable_page_config_content']['goods'] as $i=> $goods_info){
                    
                    $editable_page_config_list[$key]['goods_list'][$i]=$editable_page_config_model->getEditablePageConfigGoods($goods_info);
                    foreach($editable_page_config_list[$key]['goods_list'][$i] as $j => $goods){
                        $editable_page_config_list[$key]['goods_list'][$i][$j]['goods_image_url']=goods_thumb($goods, 240);
                    }
                }
            }
            if(isset($editable_page_config_list[$key]['editable_page_config_content']['editor'])){
                foreach($editable_page_config_list[$key]['editable_page_config_content']['editor'] as $i=> $editor_info){
                    
                    $editable_page_config_list[$key]['editable_page_config_content']['editor'][$i]=htmlspecialchars_decode($editor_info);
                }
            }
            if(isset($editable_page_config_list[$key]['editable_page_config_content']['cate'])){
              $editable_page_config_list[$key]['cate_list']=array();
                foreach($editable_page_config_list[$key]['editable_page_config_content']['cate'] as $i=> $cate_info){
                    $editable_page_config_list[$key]['cate_list'][$i]=model('editable_page_config')->getEditablePageConfigCate($cate_info,$val['editable_page_model_id']);
                    
                    
                }
                
            }
            if(isset($editable_page_config_list[$key]['editable_page_config_content']['brand'])){
              $editable_page_config_list[$key]['brand_list']=array();
              foreach($editable_page_config_list[$key]['editable_page_config_content']['brand'] as $i=> $brand_info){
                    $editable_page_config_list[$key]['brand_list'][$i]=model('editable_page_config')->getEditablePageConfigBrand($brand_info);
                    
                }
            }
            if(isset($editable_page_config_list[$key]['editable_page_config_content']['link'])){
              foreach($editable_page_config_list[$key]['editable_page_config_content']['link'] as $i=> $link_info){
                    if(isset($link_info['list'])){
                        foreach($link_info['list'] as $j => $link){
                            $editable_page_config_list[$key]['editable_page_config_content']['link'][$i]['list'][$j]['content']= htmlspecialchars_decode($link['content']);
                        }
                    }
                }
            }
        }
        $data['editable_page_config_list']=$editable_page_config_list;
        return $data;
    }
}

?>
