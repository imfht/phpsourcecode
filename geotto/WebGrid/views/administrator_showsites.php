<?php
if(isset($sites) && $sites != null){
    foreach($sites as $site){
        $img_src = ($site['icon'] == null)?$site['url']."/favicon.ico":ICON_PATH."/{$site['icon']}";
        
        echo "<div class=\"grid site-for-add\">";
        echo "<img src=\"$img_src\" />
            <div class=\"icon-name\">
                <a href=\"{$site['url']}\" target=\"_blank\">{$site['name']}</a>
            </div>
            <input type=\"hidden\" name=\"site\" value=\"{$site['id']}\" />
            <input type=\"hidden\" name=\"site_category\" value=\"{$site['category']}\" />";
        echo "</div>";
    }
}
?>