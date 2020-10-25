<?php
namespace Core\Model;
use Core\Platform\Platform;
use Think\Log;
use Think\Model;

class Processor extends Model {
    protected $autoCheckFields = false;
    /**
     * 包含关键字
     */
    const MATCH_CONTAINS = 'contains';
    /**
     * 等于关键字
     */
    const MATCH_EQUAL = 'equal';
    /**
     * 正则匹配关键字
     */
    const MATCH_REGEX = 'regex';
    /**
     * 接管对话
     */
    const MATCH_TAKEOVER = 'takeover';

    public function procText($message) {
        $equalPart = "((`msg_match`=:contains OR `msg_match`=:equal ) AND `msg_content`=:c1)";
        $regexPart = "(`msg_match`=:regex AND :c2 REGEXP `msg_content`)";
        $containsPart = "(`msg_match`=:contains AND INSTR(:c3, `msg_content`) > 0)";
        $textMatch = "(`msg_type`='" . Platform::MSG_TEXT . "' AND ({$equalPart} OR {$regexPart} OR {$containsPart}))";
        $condition = "`status`=1 AND (`msg_match`=:take OR {$textMatch})";
        $params = array();
        $params[':contains'] = self::MATCH_CONTAINS;
        $params[':equal'] = self::MATCH_EQUAL;
        $params[':regex'] = self::MATCH_REGEX;
        $params[':take'] = self::MATCH_TAKEOVER;
        
        $input = $message['content'];
        $params[':c1'] = $input;
        $params[':c2'] = $input;
        $params[':c3'] = $input;

        $processors = $this->table('__RP_PROCESSORS__')->where($condition)->bind($params)->order('`orderlist` DESC')->select();
        foreach($processors as $processor) {
            if(!empty($processor['resp_forward'])) {
                $packet = $this->createPacket(intval($processor['resp_forward']));
            } else {
                $packet = $this->execPacket($processor['from'], $message, $processor);
            }
            if(!empty($packet)) {
                return $packet;
            }
        }
    }
    
    public function procOther($message) {
        $condition = "`status`=1 AND `msg_type`=:type";
        $params = array();
        $params[':type'] = $message['type'];

        $processors = $this->table('__RP_PROCESSORS__')->where($condition)->bind($params)->order('`orderlist` DESC')->select();
        foreach($processors as $processor) {
            if(!empty($processor['resp_forward'])) {
                $packet = $this->createPacket(intval($processor['resp_forward']));
            } else {
                $packet = $this->execPacket($processor['from'], $message, $processor);
            }
            if(!empty($packet)) {
                return $packet;
            }
        }
    }
    
    private function createPacket($rid) {
        $condition = "`status`=1";
        $pars = array();
        if(is_int($rid)) {
            $condition .= " AND `id`=:id";
            $pars[':id'] = $rid;
        } else {
            $condition .= " AND `name`=:name";
            $pars[':name'] = $rid;
        }
        $reply = $this->table('__RP_REPLIES__')->where($condition)->bind($pars)->find();
        $packet = array();
        if(!empty($reply)) {
            if($reply['type'] == Platform::POCKET_TEXT) {
                $packet['type'] = $reply['type'];
                $packet['content'] = $reply['content'];
            } else {
                $packet['type'] = $reply['type'];
                $packet['news'] = unserialize($reply['content']);
            }
        }
        return $packet;
    }
    
    private function execPacket($addon, $message, $processor) {
        $class = "Addon\\{$addon}\\Api\\Executor";
        if(class_exists($class)) {
            $instance = new $class();
            if(method_exists($instance, 'exec')) {
                $packet = $instance->exec($message, $processor);
                return $packet;
            }
        }
        return null;
    }
}
