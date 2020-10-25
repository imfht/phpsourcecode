<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta http-equiv="Cache-Control" content="no-transform" />
<meta http-equiv="Cache-Control" content="no-siteapp"/>
<meta  name="viewport"  content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no">
<meta  name="format-detection"  content="telephone=no">
<link  href="<?php bloginfo('template_directory'); ?>/style.css"  type="text/css"  rel="stylesheet">
<?php if (!is_home()) { ?>
<link  type="text/css"  rel="stylesheet"  href="<?php bloginfo('template_directory'); ?>/shCoreDefault.css">
<?php } ?>
<?php if(!is_home()){auto_keywords();}else{echo '<meta name="keywords" content="';echo get_option('mytheme_keywords');echo '" />';}?>

<?php if(!is_home()){auto_description();}else{echo '<meta name="description" content="';echo get_option('mytheme_description');echo '" />';}?>

<link  rel="shortcut icon"  href="<?php bloginfo('template_directory'); ?>/favicon.ico">
<title><?php if (is_home()||is_search()) {bloginfo('name');echo ' | '.get_option('blogdescription'); }else if(is_category()||is_tag()){wp_title('');echo '_'; bloginfo('name');}else {$cat = get_the_category();wp_title('');echo '_'.$cat[0]->name.'_'; bloginfo('name');unset($cat); } ?></title></head>