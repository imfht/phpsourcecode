<?php
namespace Lcrawl;

/**
 * 正方教务系统数据查询DEMO
 * @Created by Luoning.
 * @Author: Luoning
 * @Mail luoning@luoning.me
 * @Date: 2015-04-018
 * @Time: 14:15
 * @Blog: Http://www.luoning.me
 */
 //载入框架入口
include 'run.php';


$jwid = '201201148';
$jwpwd = '*********';

/** 
 * 任务分发
***
*** 
*/

$Lcrawl->getGrade($jwid,$jwpwd);

$Lcrawl->getExam($jwid,$jwpwd);

$Lcrawl->getChooseCourses($jwid,$jwpwd);

$Lcrawl->getGradeExam($jwid,$jwpwd);

$Lcrawl->getMakeupExam($jwid,$jwpwd);

$Lcrawl->getSchedule($jwid,$jwpwd);

$Lcrawl->getGradeCount($jwid,$jwpwd);