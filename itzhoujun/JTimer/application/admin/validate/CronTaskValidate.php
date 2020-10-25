<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/3/29
 * Time: 15:13
 */

namespace app\admin\validate;


use think\Validate;

class CronTaskValidate extends Validate
{

    protected $rule = [
        'cmd'               => 'checkCmd',
        'cron_expression'   => 'checkCron'
    ];

    protected function checkCmd($value){

        if(strpos($value,'nohup') === false && strpos($value,'&') === false){
            //return '命令需使用nohup或&进行后台执行，否则会阻塞任务';
        }
        return true;

    }

    protected function checkCron($value){

        $cron = trim($value);
        $cron_arr = array_values(
            array_filter(explode(' ',$cron),function($var){
                if($var != ''){
                    return true;
                }
            }));

        if(count($cron_arr) != 6){
            return 'cron表达式需有6个域';
        }
        return true;
    }

}