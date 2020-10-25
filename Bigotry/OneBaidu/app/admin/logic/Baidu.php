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

namespace app\admin\logic;

/**
 * 百度数据逻辑
 */
class Baidu extends AdminBase
{
    
    /**
     * 获取数据列表
     */
    public function getBaiduList($where = [], $field = true, $order = '', $paginate = 0)
    {
        
        return $this->modelBaidu->getList($where, $field, $order, $paginate);
    }
    
    /**
     * 数据信息编辑
     */
    public function baiduEdit($data = [])
    {
        
        $validate_result = $this->validateBaidu->scene('edit')->check($data);
        
        if (!$validate_result) : return [RESULT_ERROR, $this->validateBaidu->getError()]; endif;
        
        $url = url('baiduList');
        
        $result = $this->modelBaidu->setInfo($data);
        
        $handle_text = empty($data['id']) ? '新增' : '编辑';
        
        $result && action_log($handle_text, '百度数据' . $handle_text . '，name：' . $data['name']);
        
        return $result ? [RESULT_SUCCESS, '操作成功', $url] : [RESULT_ERROR, $this->modelBaidu->getError()];
    }

    /**
     * 获取百度数据信息
     */
    public function getBaiduInfo($where = [], $field = true)
    {
        
        return $this->modelBaidu->getInfo($where, $field);
    }
    
    /**
     * 百度数据删除
     */
    public function baiduDel($where = [])
    {
        
        $result = $this->modelBaidu->deleteInfo($where);
        
        $result && action_log('删除', '百度数据删除' . '，where：' . http_build_query($where));
        
        return $result ? [RESULT_SUCCESS, '删除成功'] : [RESULT_ERROR, $this->modelBaidu->getError()];
    }
}
