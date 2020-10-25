<?php
if($sites == null){
    echo "";
}else{
    $i = 0;
    
    foreach($sites as $site){
        $img_src = ($site['icon'] == null || $site['icon'] == "")?
            $site['url']."/favicon.ico":
            STORAGE."/icons/{$site['icon']}";
        
        echo "<div class=\"grid icon\">";
        echo "<img src=\"$img_src\" title=\"点击打开\" />
            <div class=\"icon-name\">{$site['name']}</div>
            <input type=\"hidden\" name=\"id\" value=\"{$site['id']}\" />
            <input type=\"hidden\" name=\"index\" value=\"$i\" />
            <input type=\"hidden\" name=\"category\" value=\"{$site['category']}\" />
            <input type=\"hidden\" name=\"population\" value=\"{$site['population']}\" />
            <input type=\"hidden\" name=\"url\" value=\"{$site['url']}\" />
        ";
        echo "</div>";
        
        $i++;
    }
}
?>

<!-- 右键菜单内容 -->
<div class="contextMenu" id="contextmenu-icon" style="display:none">
    <ul>
        <li id="del">删除</li>
    </ul>
</div>