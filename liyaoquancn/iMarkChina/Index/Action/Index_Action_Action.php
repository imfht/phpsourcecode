<?php
/*
*All rights reserved: Yao Quan Li.
*Links:http://www.liyaoquan.cn.
*Links:http://www.imarkchina.cn.
*Date:2014.
*/
@include __Index__.'/Public/Resources/Config.php';
function Mark_Site_Name($Mark_P = true) {
    global $Mark_Config_Action;
    $site_name = htmlspecialchars(strip_tags($Mark_Config_Action['site_name']));
    if ($Mark_P) {
        echo $site_name;
        return;
    } 
    return $site_name;
}
function Mark_Website_Url(){
    global $Mark_Config_Action;
    $Mark_Website_Url = $Mark_Config_Action['site_link'].$Mark_Config_Action['level'].'/';
    echo $Mark_Website_Url;
}
function Mark_Site_Name_Two() {
    global $Mark_Config_Action;
   echo  htmlspecialchars(strip_tags($Mark_Config_Action['nametwo']));
}
function Mark_Site_Key($Mark_P = true) {
    global $Mark_Config_Action;
    $site_key = htmlspecialchars($Mark_Config_Action['site_key']);
    if ($Mark_P) {
        echo $site_key;
        return;
    }
    return $site_key;
}
function Mark_Site_Desc($Mark_P = true) {
    global $Mark_Config_Action;
    $site_desc = htmlspecialchars($Mark_Config_Action['site_desc']);
    if ($Mark_P) {
        echo $site_desc;
        return;
    }
    return $site_desc;
}
function Mark_Site_Link($Mark_P = true) {
    global $Mark_Config_Action;
    $site_link = $Mark_Config_Action['site_link'];
    if ($Mark_P) {
        echo $site_link;
        return;
    }
    return $site_link;
}
function Mark_Nick_Name($Mark_P = true) {
    global $Mark_Config_Action;
    $nick_name = htmlspecialchars($Mark_Config_Action['user_nick']);
    if ($Mark_P) {
        echo $nick_name;
        return;
    }
    return $nick_name;
}
function __Index__($abc) {
    global $Mark_Config_Action;
    $url = $Mark_Config_Action['site_link'] . $Mark_Config_Action['level'].'/Public/Resources/Index/' . $abc;
    echo $url;
}
function Mark_Is_Search() {
    global $Mark_Get_Type_Action;
    return $Mark_Get_Type_Action == 'search';
}
function Mark_Is_Post() {
    global $Mark_Get_Type_Action;
    return $Mark_Get_Type_Action == 'post';
}
function Mark_Is_Page() {
    global $Mark_Get_Type_Action;
    return $Mark_Get_Type_Action == 'page';
}
function Mark_Is_Tag() {
    global $Mark_Get_Type_Action;
    return $Mark_Get_Type_Action == 'tag';
}
function Mark_Is_Date() {
    global $Mark_Get_Type_Action;
    return $Mark_Get_Type_Action == 'date';
}
function Mark_Is_Archive() {
    global $Mark_Get_Type_Action;
    return $Mark_Get_Type_Action == 'archive';
}
function Mark_Tag_Name($Mark_P = true) {
    global $Mark_Get_Name_Action;
    if ($Mark_P) {
        echo htmlspecialchars($Mark_Get_Name_Action);
        return;
    }
    return $Mark_Get_Name_Action;
}
function Mark_Date_Name($Mark_P = true) {
    global $Mark_Get_Name_Action;
    if ($Mark_P) {
        echo htmlspecialchars($Mark_Get_Name_Action);
        return;
    }
    return $Mark_Get_Name_Action;
}
function Mark_Has_New() {
    global $Mark_Page_Num_Action;
    return $Mark_Page_Num_Action != 1;
}
function Mark_Has_Old() {
    global $Mark_Page_Num_Action, $Mark_Post_Coun_Action, $Mark_PageNum_Action;
    return $Mark_Page_Num_Action < ($Mark_Post_Coun_Action / $Mark_PageNum_Action);
}
function Page_List($p,$g){
    global $Mark_Post_Coun_Action,$Mark_Page_Num_Action,$Mark_PageNum_Action,$Mark_Config_Action,$Mark_Get_Name_Action,$Mark_Get_Type_Action;
    $pagelist = floor($Mark_Post_Coun_Action / $Mark_PageNum_Action);
    $pagenumber = ($pagelist - 1);
     for ($i=1; $i < $Mark_Post_Coun_Action; $i++) { 
            if ($Mark_Get_Type_Action == 'tag') {
                if ($Mark_Config_Action['write'] == 'open') {
                    $name = '/tag-';
                }else{
                    $name = '/?tag/';
                }
         }elseif($Mark_Get_Type_Action == 'date'){
         if ($Mark_Config_Action['write'] == 'open') {
            $name = '/date-';
         }else{
        $name = '/?date/';
    }
    }
    if ($Mark_Config_Action['write'] == 'open') {
       $url =  $p.'<a href="'.$Mark_Config_Action['site_link'].$Mark_Config_Action['level'].$name.$Mark_Get_Name_Action.'/page-'.$i.'">'.$i.'</a>'.$g;
    }else{
        $url =  $p.'<a href="'.$Mark_Config_Action['site_link'].$Mark_Config_Action['level'].$name.$Mark_Get_Name_Action.'/?page='.$i.'">'.$i.'</a>'.$g;
    }
        if ($i > $pagelist || $i == "7") {
            break;
        }
            echo $url;
        }
        if ($pagelist != 0) {
            echo '<span class="current">...</span>';
        }
        for ($i=4; $i < $Mark_Post_Coun_Action; $i++) { 
            if ($Mark_Get_Type_Action == 'tag') {
                if ($Mark_Config_Action['write'] == 'open') {
                    $name = '/tag-';
                }else{
                    $name = '/?tag/';
                }
         }elseif($Mark_Get_Type_Action == 'date'){
         if ($Mark_Config_Action['write'] == 'open') {
            $name = '/date-';
         }else{
        $name = '/?date/';
    }
    }
             if ($Mark_Config_Action['write'] == 'open') {
         $u =  $p.'<a href="'.$Mark_Config_Action['site_link'].$Mark_Config_Action['level'].$name.$Mark_Get_Name_Action.'/page-'.($i - 1).'">'.($i -1).'</a>'.$g;
        $r =    $p.'<a href="'.$Mark_Config_Action['site_link'].$Mark_Config_Action['level'].$name.$Mark_Get_Name_Action.'/page-'.($i - 2).'">'.($i - 2).'</a>'.$g;
        $l =     $p.'<a href="'.$Mark_Config_Action['site_link'].$Mark_Config_Action['level'].$name.$Mark_Get_Name_Action.'/page-'.($i - 3).'">'.($i - 3).'</a>'.$g;
    }else{
        $u =  $p.'<a href="'.$Mark_Config_Action['site_link'].$Mark_Config_Action['level'].$name.$Mark_Get_Name_Action.'/?page='.($i - 1).'">'.($i -1).'</a>'.$g;
        $r =   $p.'<a href="'.$Mark_Config_Action['site_link'].$Mark_Config_Action['level'].$name.$Mark_Get_Name_Action.'/?page='.($i - 2).'">'.($i - 2).'</a>'.$g;
        $l =   $p.'<a href="'.$Mark_Config_Action['site_link'].$Mark_Config_Action['level'].$name.$Mark_Get_Name_Action.'/?page='.($i - 3).'">'.($i - 3).'</a>'.$g;
    }
       if ($i >  $pagelist && $pagenumber > "6") {
        echo $l.$r.$u;
            break;
        }
        } 
}
function Mark_Goto_Old($text) {
    global $Mark_Get_Type_Action, $Mark_Get_Name_Action, $Mark_Page_Num_Action, $Mark_Config_Action;
    if ($Mark_Get_Type_Action == 'tag') {
        if ($Mark_Config_Action['write'] == 'open') {
        echo '<a href="'.$Mark_Config_Action['site_link'].$Mark_Config_Action['level'].'/tag-'.$Mark_Get_Name_Action.'/page-'.($Mark_Page_Num_Action + 1).'">'.$text.'</a>';
        }else{
        echo '<a href="'.$Mark_Config_Action['site_link'].$Mark_Config_Action['level'].'/?tag/'.$Mark_Get_Name_Action.'/?page='.($Mark_Page_Num_Action + 1). '">'.$text.'</a>';
    }
    }elseif ($Mark_Get_Type_Action == 'date') {
        if ($Mark_Config_Action['write'] =='open') {
        echo '<a href="'.$Mark_Config_Action['site_link'].$Mark_Config_Action['level'].'/date-'.$Mark_Get_Name_Action.'/page-'.($Mark_Page_Num_Action + 1).'">'.$text.'</a>';
        }else{
        echo '<a href="'.$Mark_Config_Action['site_link'].$Mark_Config_Action['level'].'/?date/'.$Mark_Get_Name_Action.'/?page='.($Mark_Page_Num_Action + 1).'">'.$text.'</a>';
    }
    }else {
       if ($Mark_Config_Action['write'] == 'open') {
         echo '<a href="'.$Mark_Config_Action['site_link'].$Mark_Config_Action['level'].'/page-'.($Mark_Page_Num_Action + 1).'">'.$text.'</a>';
        }else{
        echo '<a href="'.$Mark_Config_Action['site_link'].$Mark_Config_Action['level'].'/?page='.($Mark_Page_Num_Action + 1).'">'.$text.'</a>';
    }
}
}
function Mark_Goto_New($text) {
    global $Mark_Get_Type_Action, $Mark_Get_Name_Action, $Mark_Page_Num_Action, $Mark_Config_Action;
    if ($Mark_Get_Type_Action == 'tag') {
        if ($Mark_Config_Action['write'] == 'open') {
        echo '<a href="'.$Mark_Config_Action['site_link'].$Mark_Config_Action['level'].'/tag-'.$Mark_Get_Name_Action.'/page-'.($Mark_Page_Num_Action - 1).'">'.$text.'</a>';
        }else{
        echo '<a href="'.$Mark_Config_Action['site_link'].$Mark_Config_Action['level'].'/?tag/'.$Mark_Get_Name_Action.'/?page='.($Mark_Page_Num_Action - 1). '">'.$text.'</a>';
    }
    }elseif ($Mark_Get_Type_Action == 'date') {
         if ($Mark_Config_Action['write'] =='open') {
        echo '<a href="'.$Mark_Config_Action['site_link'].$Mark_Config_Action['level'].'/date-'.$Mark_Get_Name_Action.'/page-'.($Mark_Page_Num_Action - 1).'">'.$text.'</a>';
        }else{
        echo '<a href="'.$Mark_Config_Action['site_link'].$Mark_Config_Action['level'].'/?date/'.$Mark_Get_Name_Action.'/?page='.($Mark_Page_Num_Action - 1).'">'.$text.'</a>';
    }
    }else {
           if ($Mark_Config_Action['write'] == 'open') {
         echo '<a href="'.$Mark_Config_Action['site_link'].$Mark_Config_Action['level'].'/page-'.($Mark_Page_Num_Action - 1).'">'.$text.'</a>';
        }else{
        echo '<a href="'.$Mark_Config_Action['site_link'].$Mark_Config_Action['level'].'/?page='.($Mark_Page_Num_Action - 1).'">'.$text.'</a>';
    }
}
}
function Mark_Date_List($item_gap = '') {
    global $Mark_Dates_Action, $Mark_Config_Action;
    if (isset($Mark_Dates_Action)) {
        $date_count = count($Mark_Dates_Action);
        for ($i = 0; $i < $date_count; $i++) {
            $date = $Mark_Dates_Action[$i];
            if ($Mark_Config_Action['write'] == 'open') {
                echo '<a href="'.$Mark_Config_Action['site_link'].$Mark_Config_Action['level'].'/date-'.$date.'/">'.$date. '</a>&nbsp&nbsp&nbsp&nbsp';
            }else{
                 echo '<a href="'.$Mark_Config_Action['site_link'].$Mark_Config_Action['level'].'/?date/'.$date.'/">'.$date. '</a>&nbsp&nbsp&nbsp&nbsp';
            }
            if ($i < $date_count - 1) echo $item_gap;
        }
    }
}
function Mark_Tag_List($item_gap = '', $item_end = '&nbsp&nbsp&nbsp&nbsp') {
    global $Mark_Tag_Action, $Mark_Config_Action;
    if (isset($Mark_Tag_Action)) {
        $tag_count = count($Mark_Tag_Action);
        for ($i = 0; $i < $tag_count; $i++) {
            $tag = $Mark_Tag_Action[$i];
            if ($Mark_Config_Action['write'] == 'open') {
            echo '<a href="'.$Mark_Config_Action['site_link'].$Mark_Config_Action['level'].'/tag-'.urlencode($tag).'/">'.htmlspecialchars($tag).'</a>'.$item_end;
            }else{
            echo '<a href="'.$Mark_Config_Action['site_link'].$Mark_Config_Action['level'].'/?tag/'.urlencode($tag).'/">'.htmlspecialchars($tag).'</a>'.$item_end;
            }
            if ($i < $tag_count - 1) echo $item_gap;
        }
    }
}
function Mark_Next_Post() {
    global $Mark_Posts_Action, $Mark_Post_Ids_Action, $Mark_Post_Coun_Action, $Mark_Post_A_Action, $Mark_Post_M_End_Action, $Mark_Post_Id_Action, $Mark_Post_Action, $Mark_Page_Num_Action, $Mark_PageNum_Action;
    if (!isset($Mark_Posts_Action)) return false;
    if (!isset($Mark_Post_A_Action)) {
        $Mark_Post_A_Action = 0 + ($Mark_Page_Num_Action - 1) * $Mark_PageNum_Action;
        $Mark_Post_M_End_Action = $Mark_Post_A_Action + $Mark_PageNum_Action;
        if ($Mark_Post_Coun_Action < $Mark_Post_M_End_Action) $Mark_Post_M_End_Action = $Mark_Post_Coun_Action;
    }
    if ($Mark_Post_A_Action == $Mark_Post_M_End_Action) return false;
    if (!isset($Mark_Post_Ids_Action[$Mark_Post_A_Action])) return false;
    $Mark_Post_Id_Action = $Mark_Post_Ids_Action[$Mark_Post_A_Action];
    $Mark_Post_Action = $Mark_Posts_Action[$Mark_Post_Id_Action];
    $Mark_Post_A_Action+= 1;
    return true;
}
function Mark_The_Title($Mark_P = true) {
    global $Mark_Post_Action;
    if ($Mark_P) {
        echo htmlspecialchars(strip_tags($Mark_Post_Action['title']));
        return;
    }
    return htmlspecialchars($Mark_Post_Action['title']);
}
function Mark_The_Data($Mark_P = true) {
    global $Mark_Post_Action;
    if ($Mark_P) {
        echo $Mark_Post_Action['date'];
        return;
    }
    return $Mark_Post_Action['date'];
}
function Mark_The_Time($Mark_P = true) {
    global $Mark_Post_Action;
    if ($Mark_P) {
        echo $Mark_Post_Action['time'];
        return;
    }
    return $Mark_Post_Action['time'];
}
function Mark_The_Tags($item_begin = '', $item_gap = ', ', $item_end = '', $as_link = true) {
    global $Mark_Post_Action, $Mark_Config_Action;
    $tags = $Mark_Post_Action['tags'];
    $count = count($tags);
    for ($i = 0; $i < $count; $i++) {
        $tag = htmlspecialchars($tags[$i]);
        if ($Mark_Config_Action['write'] == 'open') {
         echo $item_begin;
        if ($as_link) {
            echo '<a href="'.$Mark_Config_Action['site_link'].$Mark_Config_Action['level'].'/tag-'.urlencode($tag).'/">';
        }
        echo $tag;
        if ($as_link) {
            echo '</a>';
        }
        echo $item_end;
        if ($i < $count - 1) echo $item_gap;
        }else{
        echo $item_begin;
        if ($as_link) {
            echo '<a href="'.$Mark_Config_Action['site_link'].$Mark_Config_Action['level'].'/?tag/'.urlencode($tag).'/">';
        }
        echo $tag;
        if ($as_link) {
            echo '</a>';
        }
        echo $item_end;
        if ($i < $count - 1) echo $item_gap;
        }

    }
}
function Mark_The_Content($Mark_P = true) {
    global $Mark_Data_Action;
    if (!isset($Mark_Data_Action)) {
        global $Mark_Post_Id_Action;
        $data = unserialize(file_get_contents(__Index__.'/Index/Point/Data/Post/Data/' . $Mark_Post_Id_Action . '.Mark'));
        $html = $data['content'];
    } else {
        $html = $Mark_Data_Action['content'];
    }
    if ($Mark_P) {
        echo $html;
        return;
    }
    return $html;
}
function Mark_The_Des() {
    global $Mark_Post_Id_Action;
    $des = unserialize(file_get_contents(__Index__.'/Index/Point/Data/Post/Data/' . $Mark_Post_Id_Action . '.Mark'));
    $des = $des['content'];
    $des = strip_tags($des);
    $des = Getstr($des, 300);
    echo $des;
}
function Mark_The_Keyword_Des() {
    global $Mark_Data_Action, $Mark_Post_Id_Action;
    $des = unserialize(file_get_contents(__Index__.'/Index/Point/Data/Post/Data/' . $Mark_Post_Id_Action . '.Mark'));
    $des = $des['content'];
    $des = strip_tags($des);
    $des = Getstr($des, 30);
    echo $des;
}
function Mark_The_Link() {
    global $Mark_Post_Id_Action, $Mark_Post_Action, $Mark_Config_Action;
    echo '<a href="';
    Mark_The_Url();
    echo '">'.htmlspecialchars($Mark_Post_Action['title']).'</a>';
}
function Mark_Post_Title(){
  global $Mark_Post_Id_Action, $Mark_Post_Action, $Mark_Config_Action;
    echo htmlspecialchars(strip_tags($Mark_Post_Action['title']));
}
function Mark_The_Url($Mark_P = true) {
    global $Mark_Post_Id_Action, $Mark_Post_Action, $Mark_Config_Action;
    if ($Mark_Config_Action['write'] == 'open') {
     $url = $Mark_Config_Action['site_link'] . $Mark_Config_Action['level'] .'/post-' . $Mark_Post_Id_Action.'.html';
       }else{
        $url = $Mark_Config_Action['site_link'] .$Mark_Config_Action['level'] . '/?post/' . $Mark_Post_Id_Action;
       }
 echo $url;
}
function Mark_Can_Comment() {
    global $Mark_Post_Id_Action, $Mark_Post_Action;
    return isset($Mark_Post_Action['can_comment']) ? $Mark_Post_Action['can_comment'] == '1' : true;
}
function Mark_Comment_Code() {
    global $Mark_Config_Action;
    echo isset($Mark_Config_Action['comment_code']) ? $Mark_Config_Action['comment_code'] : '';
}
//版权信息
function Copy_Right(){
    global $Mark_Config_Action;
    $copy_right = $Mark_Config_Action['copy_right'];
    echo $copy_right;
}
//取所有页面
function Mark_Pages($a,$b){
    global $Mark_Pages_Action,$Mark_Config_Action;
    include __Index__.'/Index/Point/Data/Page/Index/publish.php';
    $page_ids = array_keys($Mark_Pages_Action);
    $pages_id = count($page_ids);
    $page_array = array();
    $path_array = array();
    $show_array = array();
    for ($i = 0; $i < $pages_id; $i++) {
    $page_id = $page_ids[$i];
    $post = $Mark_Pages_Action[$page_id];
    $page_array = array_merge($page_array, (array)$post['title']);
    $path_array = array_merge($path_array, (array)$post['path']);
    $show_array = array_merge($show_array, (array)$post['index_show']);
   if ($show_array[$i] == 1) {
      if ($Mark_Config_Action['write'] == 'open') {
         echo $a.'<a href="'.$Mark_Config_Action['level'].'/' . $path_array[$i] . '.html">' . $page_array[$i] . '</a>'.$b;
    }else{
         echo $a.'<a href="'.$Mark_Config_Action['level'].'/?' . $path_array[$i] . '/">' . $page_array[$i] . '</a>'.$b;
    }
    }   
   }  
}
//取最新6篇日志连接
function Post_Links($a,$b){
    global $Mark_Posts_Action,$Mark_Config_Action;
    include __Index__.'/Index/Point/Data/Post/Index/publish.php';
    $page_ids = array_keys($Mark_Posts_Action);
    $pages_id = count($page_ids);
    $page_array = array();
    $path_array = array();
    for ($i = 0; $i < $pages_id; $i++) {
    $page_id = $page_ids[$i];
    $post = $Mark_Posts_Action[$page_id];
    $page_array = array_merge($page_array, (array)$post['id']);
    $path_array = array_merge($path_array, (array)$post['title']);
    $post_link = $page_array[$i] ;
    $post_title = $path_array[$i];
    if ($i ==6) {
       break;
   }
   if ($Mark_Config_Action['write'] == 'open') {
    echo  $a.'<a href="'.$Mark_Config_Action['level'].'/post-'.$post_link.'.html" title="'.$post_title.'">'.$post_title.'</a>'.$b;
  
   }else{ 
    echo  $a.'<a href="'.$Mark_Config_Action['level'].'/?post/'.$post_link.'" title="'.$post_title.'">'.$post_title.'</a>'.$b;
}
 }
}
//友情链接
function Mark_Links($a,$b){
    global $Mark_Links_Action;
    include __Index__.'/Index/Point/Data/Links/Index/publish.php';
    $Link_ids = array_keys($Mark_Links_Action);
    $Links__id = count($Link_ids);
    $Link_array = array();
    $Links_array = array();
    for ($i = 0; $i < $Links__id; $i++) {
    $Link_id = $Link_ids[$i];
    $post = $Mark_Links_Action[$Link_id];
    $Link_array = array_merge($Link_array, (array)$post['title']);
    $Links_array = array_merge($Links_array,(array)$post['url']);
    echo $a.'<a href="' . $Links_array[$i] . '" target="_blank">' . $Link_array[$i] . '</a>'.$b;
    }
}
function Mark_Right($a,$b){
    global $Mark_Config_Action;
    if ($Mark_Config_Action['write'] == 'open') {
    echo $a.'<a href="'.$Mark_Config_Action['level'].'/">首页</a>' .$b;
    echo $a.'<a href="'.$Mark_Config_Action['level'].'/archive.html">归档</a>' .$b;
    }else{
    echo $a.'<a href="'.$Mark_Config_Action['level'].'/">首页</a>' .$b;
    echo $a.'<a href="'.$Mark_Config_Action['level'].'/?archive/">归档</a>' .$b;
    }
}
function Mark_Rss($a,$b){
     global $Mark_Config_Action;
      if ($Mark_Config_Action['write'] == 'open') {
    echo $a.'<a href="'.$Mark_Config_Action['level'].'/rss.xml" target="_blank">Rss</a>'.$b;
    }else{
    echo $a.'<a href="'.$Mark_Config_Action['level'].'/?rss/" target="_blank">Rss</a>'.$b;
    }
}
function Mark_Images(){
    global $Mark_Posts_Action,$Mark_Post_Id_Action;
    $imagesurl = array();
    $imagesurl = $Mark_Posts_Action[$Mark_Post_Id_Action];
    echo $imagesurl['imagesurl'];
}
function Mark_Runtime(){
    global $Mark_Config_Action;
    if ($Mark_Config_Action['runyear'] != '' && $Mark_Config_Action['runmon'] != '' && $Mark_Config_Action['runday'] != '' && $Mark_Config_Action['runhour'] != '' && $Mark_Config_Action['runmin'] != '' && $Mark_Config_Action['runsec'] != '') {
        echo '<script language=javascript>
function show_date_time(){
window.setTimeout("show_date_time()", 1000);
BirthDay=new Date("'.$Mark_Config_Action['runmon'].'/'.$Mark_Config_Action['runday'].'/'.$Mark_Config_Action['runyear'].' '.$Mark_Config_Action['runhour'].':'.$Mark_Config_Action['runmin'].':'.$Mark_Config_Action['runsec'].'");
today=new Date();
timeold=(today.getTime()-BirthDay.getTime());
sectimeold=timeold/1000
secondsold=Math.floor(sectimeold);
msPerDay=24*60*60*1000
e_daysold=timeold/msPerDay
daysold=Math.floor(e_daysold);
e_hrsold=(e_daysold-daysold)*24;
hrsold=Math.floor(e_hrsold);
e_minsold=(e_hrsold-hrsold)*60;
minsold=Math.floor((e_hrsold-hrsold)*60);
seconds=Math.floor((e_minsold-minsold)*60);
span_dt_dt.innerHTML='.Mark_Site_Name().'"已运行："+daysold+"天"+hrsold+"小时"+minsold+"分"+seconds+"秒";
}
show_date_time();
</script>';
    }
}
function Mark_Index_Tag(){
    global $Mark_Config_Action,$Mark_Posts_Action;
     include __Index__.'/Index/Point/Data/Post/Index/publish.php';
   $Mark_Post_Ids_Action = array_keys($Mark_Posts_Action);
    $Mark_Post_Coun_Action = count($Mark_Post_Ids_Action);
    $tags_array = array();
     for ($i = 0; $i < $Mark_Post_Coun_Action; $i++) {
        $post_id = $Mark_Post_Ids_Action[$i];
        $post = $Mark_Posts_Action[$post_id];
        $tags_array = array_merge($tags_array, (array)$post['tags']);
    }
    $Mark_Tag_Action = array_values(array_unique($tags_array));
    if (isset($Mark_Tag_Action)) {
        $tag_count = count($Mark_Tag_Action);
        for ($i = 0; $i < $tag_count; $i++) {
             $tag = $Mark_Tag_Action[$i];
            if ($Mark_Config_Action['write'] == 'open') {
            echo '<a href="'.$Mark_Config_Action['site_link'].$Mark_Config_Action['level'].'/tag-'.urlencode($tag).'/">'.htmlspecialchars($tag).'</a>'.$item_end;
            }else{
            echo '<a href="'.$Mark_Config_Action['site_link'].$Mark_Config_Action['level'].'/?tag/'.urlencode($tag).'/">'.htmlspecialchars($tag).'</a>'.$item_end;
            }
            if ($i < $tag_count - 1) echo $item_gap;
        }
    }
}
function Mark_Index_Date($a,$b){
     global $Mark_Config_Action,$Mark_Posts_Action;
      include __Index__.'/Index/Point/Data/Post/Index/publish.php';
    $Mark_Post_Ids_Action = array_keys($Mark_Posts_Action);
    $Mark_Post_Coun_Action = count($Mark_Post_Ids_Action);
    $date_array = array();
    for ($i = 0; $i < $Mark_Post_Coun_Action; $i++) {
        $post_id = $Mark_Post_Ids_Action[$i];
        $post = $Mark_Posts_Action[$post_id];
        $date_array[] = substr($post['date'], 0, 7);
    }
    $Mark_Dates_Action = array_values(array_unique($date_array));
     if (isset($Mark_Dates_Action)) {
        $date_count = count($Mark_Dates_Action);
        for ($i = 0; $i < $date_count; $i++) {
            $date = $Mark_Dates_Action[$i];
            if ($i ==6) {
                break;
            }
            if ($Mark_Config_Action['write'] == 'open') {
                echo $a.'<a href="'.$Mark_Config_Action['site_link'].$Mark_Config_Action['level'].'/date-'.$date.'/">'.$date. '</a>'.$b;
            }else{
                 echo $a.'<a href="'.$Mark_Config_Action['site_link'].$Mark_Config_Action['level'].'/?date/'.$date.'/">'.$date. '</a>'.$b;
            }
        }
    }
}
function Root_Login(){
    global $Mark_Config_Action;
    $index = $Mark_Config_Action['site_link'];
     $root_file = $Mark_Config_Action['root_file'];
    $level = $Mark_Config_Action['level'];
    echo '<a href="'.$index.$level.'/'.$root_file.'">Login</a>';
}
function Mark_Hits(){
    global $Mark_Post_Id_Action,$hits,$hits_tmp,$hits_auto;
$hits_file = __Index__.'/Index/Point/Hits/hits.php';
$hits_tmp = __Index__.'/Index/Point/Hits/hits_tmp.php'; 
$hits_auto_tmp = __Index__.'/Index/Point/Hits/hits_auto.php'; 
$post_hit[$Mark_Post_Id_Action] = 1;
$code_tmp = "<?php \n\$hits_tmp=".var_export($post_hit,true)."\n?>";
file_put_contents($hits_tmp,$code_tmp);
include $hits_file;
include $hits_tmp;
include $hits_auto_tmp;
if (!isset($hits[$Mark_Post_Id_Action])) {
        $hit_post = array_merge($hits,$hits_tmp);
        $put_hit = "<?php \n\$hits=".var_export($hit_post,true)."\n?>";
        file_put_contents($hits_file,$put_hit);
}else{
    $hitpost = count($hits);
  for ($i=0; $i < $hitpost; $i++) { 
      unset($hitpost);
      $hitpost = $hits[$Mark_Post_Id_Action];
  }
     $auto_hits[$Mark_Post_Id_Action] = $hitpost+1;
    $code_auto = "<?php \n\$hits_auto=".var_export($auto_hits,true)."\n?>";
    file_put_contents($hits_auto_tmp,$code_auto);
    $last_hit_auto = array_merge($hits,$hits_auto);
    $last_hits_add = "<?php \n\$hits=".var_export($last_hit_auto,true)."\n?>";
    file_put_contents($hits_file,$last_hits_add);
}
}
function Mark_The_Hits(){
    global $Mark_Post_Id_Action,$hits;
    include __Index__.'/Index/Point/Hits/hits.php';
    if ($hits[$Mark_Post_Id_Action] != '') {
        echo $hits[$Mark_Post_Id_Action];
    }else{
        echo 0;
    }
}
function Mark_Hot_Post($a,$b){
    global $hits,$Mark_Posts_Action,$Mark_Config_Action;
    include __Index__.'/Index/Point/Hits/hits.php';
    include __Index__.'/Index/Point/Date/Post/Index/publish.php';
    $title_hit = array();
    $hitss = array();
    foreach ($hits as $key => $value) {
        $title_hit[] = $key;
        $hitss[] = $value;
    }
    for ($i=0; $i < 6; $i++) { 
       $arr = $Mark_Posts_Action[$title_hit[$i]];
           $title = $arr['title'];
           $id = $arr['id'];
        if ($Mark_Config_Action['write'] == 'open') {
    echo  $a.'<a href="'.$Mark_Config_Action['level'].'/post-'. $id.'.html" title="'.$title.'">'.$title.'</a>'.$b;
   }else{ 
    echo  $a.'<a href="'.$Mark_Config_Action['level'].'/?post/'. $id.'" title="'.$title.'">'.$title.'</a>'.$b;
}
 }
}
?>