<?php
namespace app\common\model;

use think\Model;
use think\Db;

class Action extends Model
{
	/**
     * 新增或更新一个行为
     * @return boolean fasle 失败 ， int  成功 返回完整的数据
     */
    public function editAction($data){

        if(empty($data)){
            return false;
        }

        if(!empty($data['action_rule'])){
        	$action_rule = $data['action_rule'];
	        if(!empty($action_rule)){

	            for($i=0;$i<count($action_rule['table']);$i++){
	                $data['rule'][] = [
	                    'table'=>$action_rule['table'][$i],
	                    'field'=>$action_rule['field'][$i],
	                    'rule'=>$action_rule['rule'][$i],
	                    'cycle'=>$action_rule['cycle'][$i],
	                    'max'=>$action_rule['max'][$i],
	                ];
	            }
	        }
        }
        
        
        if(empty($data['rule'])){
            $data['rule'] ='';
        }else{
            $data['rule'] = serialize($data['rule']);
        }
        
        unset($data['action_rule']);
        /* 添加或新增行为 */
        if(empty($data['id'])){ //新增数据
            
            $res = $this->allowField(true)->save($data); //添加行为
            if(!$res){
                $this->error = lang('_NEW_BEHAVIOR_WITH_EXCLAMATION_');
                return false;
            }
        } else { //更新数据
            $res = $this->allowField(true)->save($data,['id'=>$data['id']]); //更新基础内容
            if(!$res){
                $this->error = lang('_UPDATE_BEHAVIOR_WITH_EXCLAMATION_');
                return false;
            }
        }
        //删除缓存
        cache('action_list', null);

        //内容添加或更新完成
        return $data;
    }


    public function getAction($map){
        $result = collection($this->where($map)->select())->toArray();
        foreach ($result as &$v) {
        	if($v['module']=='' || empty($v['module'])) {
        		//默认系统行为模块名
        		$v['module'] = 'admin';
        	}
        }
        unset($v);
        return $result;
    }

    public function getActionOpt(){
        $result = collection($this->where(['status'=>1])->field('name,title')->select())->toArray();
        return $result;
    }

    public function getActionName($key){
        !is_array($key) && $key = explode(',',str_replace(array('[',']'),'',$key));
        $return = array();
        foreach($key as $val){
            $return[] = $this->where(['name'=>$val])->value('title');
        }
        
        return implode(',',$return);
    }

    public function getListByPage($map,$order='create_time desc',$field='*',$r=20)
    {
        $list = $this->where($map)->order($order)->field($field)->paginate($r,false,['query'=>request()->param()]);
        return $list;
    }

	/**
	 * 记录行为日志，并执行该行为的规则
	 * @param string $action 行为标识
	 * @param string $model 触发行为的模型名
	 * @param int $record_id 触发行为的记录id
	 * @param int $user_id 执行行为的用户id
	 * @return boolean
	 */
	public function action_log($action = null, $model = null, $record_id = null, $user_id = null)
	{	
	    //参数检查
	    if (empty($action) || empty($model) || empty($record_id)) {
	        return lang('_PARAMETERS_CANT_BE_EMPTY_');
	    }
	    if (empty($user_id)) {
	        $user_id = is_login();
	    }

	    //查询行为,判断是否执行
	    $action_info = $this->where(['name'=>$action])->find();

	    if ($action_info['status'] != 1) {
	        return lang('_THE_ACT_IS_DISABLED_OR_DELETED_');
	    }

	    //插入行为日志
	    $data['action_id'] = $action_info['id'];
	    $data['user_id'] = $user_id;
	    $data['action_ip'] = request()->ip(1);
	    $data['model'] = $model;
	    $data['record_id'] = $record_id;
	    $data['create_time'] = time();

	    //解析日志规则,生成日志备注
	    if (!empty($action_info['log'])) {
	        if (preg_match_all('/\[(\S+?)\]/', $action_info['log'], $match)) {
	            $log['user'] = $user_id;
	            $log['record'] = $record_id;
	            $log['model'] = $model;
	            $log['time'] = time();
	            $log['data'] = ['user' => $user_id, 'model' => $model, 'record' => $record_id, 'time' => time()];
	            
	            
	            if(isset($match[1])){
	            	foreach ($match[1] as $value) {
		                $param = explode('|', $value);
		                if (isset($param[1])) {
		                    $replace[] = call_user_func($param[1], $log[$param[0]]);
		                } else {
		                    $replace[] = $log[$param[0]];
		                }
		            }
	            }
	            
	            $data['remark'] = str_replace($match[0], $replace, $action_info['log']);
	            
	        } else {
	            $data['remark'] = $action_info['log'];
	        }

	    } else {
	        //未定义日志规则，记录操作url
	        $data['remark'] = '操作url：' . $_SERVER['REQUEST_URI'];
	    }

	    $log_id = Db::name('ActionLog')->insertGetId($data);

	    //解析积分规则并执行
	    if (!empty($action_info['rule'])) {
	        //解析行为
	        $rules = $this->parse_action($action, $user_id);
	        //执行行为
	        $res = $this->execute_action($rules, $action_info['id'], $user_id, $log_id);

	        return $res;
	    }

	    return true;
	}

	/**
	 * 解析行为规则
	 * 规则定义  table:$table|field:$field|condition:$condition|rule:$rule[|cycle:$cycle|max:$max][;......]
	 * 规则字段解释：table->要操作的数据表，不需要加表前缀；
	 *              field->要操作的字段；
	 *              condition->操作的条件，目前支持字符串，默认变量{$self}为执行行为的用户
	 *              rule->对字段进行的具体操作，目前支持四则混合运算，如：1+score*2/2-3
	 *              cycle->执行周期，单位（小时），表示$cycle小时内最多执行$max次
	 *              max->单个周期内的最大执行次数（$cycle和$max必须同时定义，否则无效）
	 * 单个行为后可加 ； 连接其他规则
	 * @param string $action 行为id或者name
	 * @param int $self 替换规则里的变量为执行用户的id
	 * @return boolean|array: false解析出错 ， 成功返回规则数组
	 * @author huajie <banhuajie@163.com>
	 */
	public function parse_action($action = null, $self)
	{
	    if (empty($action)) {
	        return false;
	    }

	    //参数支持id或者name
	    if (is_numeric($action)) {
	        $map = array('id' => $action);
	    } else {
	        $map = array('name' => $action);
	    }

	    //查询行为信息
	    $info = Db::name('Action')->where($map)->find();

	    if (!$info || $info['status'] != 1) {
	        return false;
	    }


	    //解析规则:table:$table|field:$field|condition:$condition|rule:$rule[|cycle:$cycle|max:$max][;......]
	    $rules = unserialize($info['rule']);

	    foreach ($rules as $key => &$rule) {
	        foreach ($rule as $k => &$v) {
	            if (empty($v)) {
	                unset($rule[$k]);
	            }
	        }
	        unset($k, $v);
	    }
	    unset($key, $rule);

	    $rules = str_replace("{$self}", $self, $rules);
	    
	    return $rules;
	}

	/**
	 * 执行行为
	 * @param array $rules 解析后的规则数组
	 * @param int $action_id 行为id
	 * @param array $user_id 执行的用户id
	 * @return boolean false 失败 ， true 成功
	 * @author huajie <banhuajie@163.com>
	 */
	public function execute_action($rules = false, $action_id = null, $user_id = null, $log_id = null)
	{
	    hook('handleAction',array('action_id'=>$action_id,'user_id'=>$user_id,'log_id'=>$log_id,'log_score'=>&$log_score));

	    if (!$rules || empty($action_id) || empty($user_id)) {
	        return false;
	    }
	    $return = true;

	    $action_log = Db::name('ActionLog')->where(['id' => $log_id])->find();

	    
	    //行为日志在微信登陆时报错
	    foreach ($rules as $rule) {

	        //检查执行周期
	        $map = ['action_id' => $action_id, 'user_id' => $user_id];
	        $map['create_time'] = ['gt', time() - intval($rule['cycle']) * 3600];
	        $exec_count = Db::name('ActionLog')->where($map)->count();
	        if ($exec_count > $rule['max']) {
	            continue;
	        }

	        //获取现在的积分数量
	    	$Model = Db::name(ucfirst($rule['table']));
	        $field = 'score' . $rule['field'];
	        $nowScore = $Model->where(['uid' => $user_id])->value($field);
	        $rule['rule'] = (is_bool(strpos($rule['rule'], '+')) ? '+' : '') . $rule['rule'];
	        $rule['rule'] = is_bool(strpos($rule['rule'], '-')) ?  $rule['rule'] : substr($rule['rule'],1) ;
	        //应该设置的积分
	        $newScore = floatval($nowScore)+$rule['rule'];
	        //设置积分 //执行数据库操作
	        $res = $Model->where(['uid' => $user_id, 'status' => 1])->setField($field, $newScore);
	        if (!$res) {
	            $return = false;
	        }else{
	        	$scoreModel= model('ucenter/Score');
		        $scoreModel->cleanUserCache($user_id,$rule['field']);

		        $sType = Db::name('ucenter_score_type')->where(['id' => $rule['field']])->find();
		        $log_score .= '【' . $sType['title'] . '：' . $rule['rule'] . $sType['unit'] . '】';

		        $action = strpos($rule['rule'], '-')?'dec':'inc';
		        //写积分日志
		        $scoreModel->addScoreLog($user_id,$rule['field'],$action , substr($rule['rule'],1,strlen($rule['rule'])-1),$action_log['model'],$action_log['record_id'],$action_log['remark'].'【' . $sType['title'] . '：' . $rule['rule'] . $sType['unit'] . '】');
	        }

	        if (isset($log_score)) {
		        cookie('score_tip', $log_score, 30);
		        Db::name('ActionLog')->where(['id' => $log_id])->setField('remark', $action_log['remark'] .','. $log_score);
		    }
	    }
	    
	    return $return;
	}

}