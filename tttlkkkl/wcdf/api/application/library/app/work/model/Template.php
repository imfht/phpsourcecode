<?php
/**
 *
 * Date: 17-5-6
 * Time: 下午11:47
 * author :李华 yehong0000@163.com
 */

namespace app\work\model;


class Template
{
    protected static $Obj;
    protected $table='work_template';



    /**
     * 初始化
     */
    protected function initialize()
    {
        parent::initialize();
    }
    /**
     * @return Work
     */
    public static function getInstance()
    {
        if(!self::$Obj){
            self::$Obj=new self;
        }
        return self::$Obj;
    }

    public function getCurrentWorkInfo(){
        return [
            ''
        ];
    }
}