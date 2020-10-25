<?php
if($icons == null){
    echo "暂无数据";
}else{
    foreach($icons as $icon){
        $img_src = ($icon['url'] == null)?ICON_PATH."/default.png":ICON_PATH."/{$icon['url']}";
        
        echo "<div class=\"grid\">";
        echo "<img src=\"$img_src\" />
            <div class=\"icon-name\">{$icon['name']}</div>
            <input type=\"hidden\" name=\"icon\" value=\"{$icon['id']}\" />";
        echo "</div>";
    }
}
?>