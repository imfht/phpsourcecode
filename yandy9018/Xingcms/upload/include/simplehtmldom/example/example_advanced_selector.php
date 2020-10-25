<?php

include('../simple_html_dom.php');
$str = "<div>
    <div>
        <div class=\"foo bar\">ok</div>
    </div>
</div>";
$html = str_get_html($str);
echo $html->find('div div div',0)->innertext .'<br>';
$str = "<ul id=\"ul1\">
    <li>item:<span>1</span></li>
    <li>item:<span>2</span></li>
</ul>
<ul id=\"ul2\">
    <li>item:<span>3</span></li>
    <li>item:<span>4</span></li>
</ul>";
$html = str_get_html($str);
foreach($html->find('ul') as $ul) {
foreach($ul->find('li') as $li)
echo $li->innertext .'<br>';
}
$str = "<form name=\"form1\" method=\"post\" action=\"\">
    <input type=\"checkbox\" name=\"checkbox1\" value=\"checkbox1\" checked>item1<br>
    <input type=\"checkbox\" name=\"checkbox2\" value=\"checkbox2\">item2<br>
    <input type=\"checkbox\" name=\"checkbox3\" value=\"checkbox3\" checked>item3<br>
</form>";
$html = str_get_html($str);
foreach($html->find('input[type=checkbox]') as $checkbox) {
if ($checkbox->checked)
echo $checkbox->name .' is checked<br>';
else
echo $checkbox->name .' is not checked<br>';
}

?>