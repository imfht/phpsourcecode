<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2014/11/17
 * Time: 16:43
 */

namespace Home\Model;


class ResumeModel extends CommonModel{

    /**
     * 字段自动完成
     */
    protected $_auto = array(
        // 创建时间
        array('public_time', 'time', 1, 'function')

    );

    protected $_validate = array(
        // 不能为空
        array('name', 'require', '姓名不能为空！', 1, 'regex', 3),
        array('sex', 'require', '性别不能为空！', 1, 'regex', 3),
        array('borth_year', 'require', '出生年份不能为空！', 1, 'regex', 3),
        array('borth_month', 'require', '出生月份不能为空！', 1, 'regex', 3),
        array('borth_day', 'require', '出生天数不能为空！', 1, 'regex', 3),
        array('marry', 'require', '婚姻状况不能为空！', 1, 'regex', 3),
        array('education', 'require', '学历不能为空！', 1, 'regex', 3),
        array('profession', 'require', '专业不能为空！', 1, 'regex', 3),
        array('school', 'require', '毕业院校不能为空！', 1, 'regex', 3),
        array('job_name', 'require', '应聘岗位不能为空！', 1, 'regex', 3),
        array('transfer_job', 'require', '能否调岗不能为空！', 1, 'regex', 3),
       // array('hope_job_address', 'require', '出生年份不能为空！', 1, 'regex', 3),
        array('salary', 'require', '期望薪酬不能为空！', 1, 'regex', 3),
        array('tel', 'require', '联系电话不能为空！', 1, 'regex', 3),
        array('address', 'require', '家庭住址不能为空！', 1, 'regex', 3),
        array('education_career', 'require', '教育经历不能为空！', 1, 'regex', 3),
        array('job_career', 'require', '工作经历不能为空！', 1, 'regex', 3),





        array('name', '0,20', '姓名长度不能超过20个字符！', 1, 'length', 3),
        array('education', '0,20', '学历长度不能超过20个字符！', 1, 'length', 3),
        array('profession', '0,50', '专业长度不能超过50个字符！', 1, 'length', 3),
        array('school', '0,100', '毕业院校长度不能超过100个字符！', 1, 'length', 3),
        array('job_name', '0,20', '应聘岗位长度不能超过20个字符！', 1, 'length', 3),
        array('salary', 'currency', '期望薪酬格式不正确！', 1, 'regex', 3),
        array('tel', '/^(\+?86-?)?(18|15|13)[0-9]{9}$|^(010|02\d{1}|0[3-9]\d{2})-\d{7,9}(-\d+)?$/', '联系电话格式不正确！', 1, 'regex', 3),


    );

} 