<?php
namespace Addon\Bridge\Model;
use Core\Model\Addon;
use Think\Model;

class Bridge extends Model {
    protected $autoCheckFields = false;
    /**
     * @var Addon
     */
    private $addon;

    function __construct($addon) {
        parent::__construct();
        $this->addon = $addon;
    }
    
    public function getOne($id, $isProcessor = false) {
        if($isProcessor) {
            $condition = '`processor`=:id';
        } else {
            $condition = '`id`=:id';
        }
        $pars = array();
        $pars[':id'] = $id;
        $platform = $this->table('__BR_BRIDGES__')->where($condition)->bind($pars)->find();
        return $platform;
    }

    public function create($record) {
        $rec = $this->table('__BR_BRIDGES__')->data($record)->add();
        if(!empty($rec)) {
            $id = $this->getLastInsID();
            $pid = $this->addon->registerTakeOver(0, $id, 0, '对接' . $record['title']);
            if(!is_error($pid)) {
                $rec = array();
                $rec['processor'] = $pid;
                $this->table('__BR_BRIDGES__')->data($rec)->where("`id`='{$id}'")->save();
            }
        } else {
            $id = 0;
        }
        
        return $id;
    }
    
    public function remove($id) {
        $platform = $this->getOne($id);
        if(empty($platform)) {
            return error(-1, '访问错误');
        }
        $this->table('__BR_BRIDGES__')->where("`id`='{$id}'")->delete();
        return $this->addon->unRegister($id);
    }
}
