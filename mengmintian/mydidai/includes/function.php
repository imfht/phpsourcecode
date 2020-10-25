<?php
/**
 * 自定义的函数文件
 */


/**
 * 加载模板文件
 * @param  [type] $tplname [description]
 * @return [type]          [description]
 */
function loadTpl($tplname){
    $tplpath = TEMP_DIR . $tplname . '.html';
    if(!empty($tplname) && file_exists($tplpath)){
        return require $tplpath;
    }
}

/**
 * 获取指定aid的文章详情url
 * @param type $aid
 * @return type
 */
function article_url($aid){
    return 'article.php?id=' . $aid;
}

/**
 * 根据栏目cid获取栏目的url
 * @param type $cid
 * @return type
 */
function nav_url($cid){
    return 'list.php?id=' . $cid;
}

function column_name($c_id){
    $_sql = "SELECT name FROM my_column WHERE id='{$c_id}' LIMIT  1";
    $model = new Model();
    $field = $model->getOne($_sql);
    return $field;
}

function show_error($error){
    if(!IS_DEBUG){
        echo '<script>alert("网站出现错误！");</script>';
    }
}

