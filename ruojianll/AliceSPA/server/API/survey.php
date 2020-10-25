<?php

$app->post('/survey/confirm',function()use($app){
    $app->utility->openSEO("完成");
    $app->utility->SEOH1("已成功提交问卷，感谢您的参与！");
    $s = new RjSurvey();
    $arr = $s->columnMap();
    foreach($arr as $key => $value){
        if($key==="id" || $key==="date")
            continue;
        $s->$key = $_POST[$key];
    }
    $s->date=getMysqlDateTimeNow();
    $s->create();
});

function getImageRank($num){
    if($num <=1)
        return 1;
    if($num <=3)
        return 2;
    if($num <=7)
        return 3;
    if($num <=9)
        return 4;
    if($num <=10)
        return 5;
}
function getBoolean($num){
    return $num==1?"是":"否";
}
function getEducationRank($num){
    if($num <= 1)
        return "小学以下";
    if($num <= 2)
        return "小学";
    if($num <= 3)
        return "初中";
    if($num <= 4)
        return "高中";
    if($num <= 5)
        return "大学";
    if($num <= 6)
        return "研究生";
    if($num <= 7)
        return "博士";
    if($num <= 8)
        return "中专";
    if($num <= 9)
        return "大专";
}

function createMainTable($app,$exp=0){
    echo '<table class="table-bordered table-striped table-hover">';
    echo "<tr><td>ID</td>";
    for($i=0;$i<70;$i++){
        echo "<td>".($i+1)."</td>";
    }
    echo "<td>喜欢原因</td><td>不喜欢原因</td><td>相关专业</td><td>太湖景区</td><td>年龄</td><td>教育水平</td><td>职业</td><td>专业</td><td>家乡</td><td>其他</td></tr>";
    $res = RjSurvey::find();
    $imgs = array();
    for($i=0;$i<70;$i++){
        $imgs[$i] = 0;
    }
    $ages = array();
    foreach($res as $item){
        if($exp != 0){
            if($exp != $item->experience)
                continue;
        }
        echo "<tr>";
        echo "<td>".$item->id."</td>";
        for($i=0;$i<70;$i++){
            $t = "cimg".($i+1);
            $rank = $item->$t/*getImageRank($item->$t)*/;
            echo "<td>".$rank."</td>";
            $imgs[$i]+=$rank;
        }
        echo "<td>".$item->reason_like."</td>";
        echo "<td>".$item->reason_dislike."</td>";
        echo "<td>".getBoolean($item->experience)."</td>";
        echo "<td>".getBoolean($item->visited)."</td>";
        echo "<td>".$item->age."</td>";
        if(!isset($ages[$item->age]))
            $ages[$item->age]=1;
        else
            $ages[$item->age]++;
        echo "<td>".getEducationRank($item->level)."</td>";
        echo "<td>".$item->job."</td>";
        echo "<td>".$item->region."</td>";
        echo "<td>".$item->province."</td>";
        echo "<td>".$item->comment."</td>";
        echo "</tr>";
    }
    echo "<tr>";
    echo "<td>总和</td>";
    for($i=0;$i<70;$i++){
        echo "<td>".$imgs[$i]."</td>";
    }
    echo "</tr>";
    echo "</table>";

    ksort($ages);
    echo '<table class="table-bordered table-striped table-hover">';
    echo "<tr><td>年龄</td>";
    foreach($ages as $key => $value){
        echo "<td>".$key."</td>";
    }
    echo "<br></tr>";
    echo "<tr><td>数量</td>";
    foreach($ages as $key => $value){
        echo "<td>".$value."</td>";
    }
    echo "</tr>";
    echo '</table>';
}

$app->get('/survey/result',function()use($app){
    $app->utility->closeApi();
    echo '<!DOCTYPE html><html lang="zh"><head><meta charset="utf-8"><link rel="stylesheet" href="/css/bootstrap.min.css" type="text/css" charset="UTF-8"><title>问卷结果</title></head><body>';

    $phql = 'SELECT COUNT(*) as c FROM RjSurvey';
    $res = $app->modelsManager->executeQuery($phql);
    $count = null;
    foreach($res as $i){
        $count = $i->c;
    }
    echo "<h3>全部 ".$count." 份</h3>";
    createMainTable($app,0);
    $phql = 'SELECT COUNT(*) as c FROM RjSurvey WHERE experience = 1';
    $res = $app->modelsManager->executeQuery($phql);
    $count = null;
    foreach($res as $i){
        $count = $i->c;
    }
    echo "<hr><h3>相关专业 ".$count." 份</h3>";
    createMainTable($app,1);
    $phql = 'SELECT COUNT(*) as c FROM RjSurvey WHERE experience = 2';
    $res = $app->modelsManager->executeQuery($phql);
    $count = null;
    foreach($res as $i){
        $count = $i->c;
    }
    echo "<hr><h3>非相关专业 ".$count." 份</h3>";
    createMainTable($app,2);
    echo '</body></html>';

});