<?php

namespace Admin\Controller;

use Think\Controller;

class CharController extends CommonController {
    /*
     * 文章图表统计
     */

    public function content() {
        $this->title='文章月份统计';
        $mouth = array(0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0);
        $y = I('post.year') + 0;
        $y_time = clmao_getYearTime($y);
        $year = date('Y', time());
        if($y!=0){
            $year = $y;
        }
        $time = M('content')->where('status=1 and time>=' . $y_time)->getField('time', true);
        foreach ($time as $key => $value) {
            switch (date('Ym', $value)) {
                case $year . '01':
                    $mouth[0] ++;
                    break;
                case $year . '02':
                    $mouth[1] ++;
                    break;
                case $year . '03':
                    $mouth[2] ++;
                    break;
                case $year . '04':
                    $mouth[3] ++;
                    break;
                case $year . '05':
                    $mouth[4] ++;
                    break;
                case $year . '06':
                    $mouth[5] ++;
                    break;
                case $year . '07':
                    $mouth[6] ++;
                    break;
                case $year . '08':
                    $mouth[7] ++;
                    break;
                case $year . '09':
                    $mouth[8] ++;
                    break;
                case $year . '10':
                    $mouth[9] ++;
                    break;
                case $year . '11':
                    $mouth[10] ++;
                    break;
                case $year . '12':
                    $mouth[11] ++;
                    break;
                default:
                    break;
            }
        }
        $this->year = $year;
        $this->all_year_num = array_sum($mouth);
        $this->mouth = $mouth;
        $this->display();
    }

    /*
     * 文章分类报表统计
     */

    public function category() {
        $this->title='文章分类统计';
        $c_title = M('category')->getField('title', true);
        $c_num = M('content')->where('status=1')->group('c_id')->getField('count(*)', true);
        $titleStr = '';
        $jsonStr = '';
        foreach ($c_title as $key => $value) {
            $c_num[$key] = $c_num[$key] ? $c_num[$key] : 0;
            $jsonStr .= "{value:$c_num[$key],name:'$value'},";
            $titleStr .= "'$value',";
        }
        $this->titleStr = $titleStr;
        $this->jsonStr = $jsonStr;
        $this->display();
    }

    public function mouth() {
        $this->display();
    }

}
