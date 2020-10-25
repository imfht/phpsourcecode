<?php 
/*
 *修改默认设置
 */
require get_template_directory() . '/inc/plugins/settings.php';

/*
 * 面包屑
 */
require get_template_directory() . '/inc/plugins/full-breadcrumb.php';

/*
 * 更改wordpress一些安全设置
 */
require get_template_directory() . '/inc/plugins/save.php';

/*
 * 开启smtp发送email
 */
require get_template_directory() . '/inc/plugins/email.php';

/*
 * 开启304 返回，提高网站效率,开发阶段不建议开启
 */
require get_template_directory() . '/inc/plugins/304-respond.php';

/*
 * 缓存菜单
 */
require get_template_directory() . '/inc/plugins/wp-nav-menu-cache.php';