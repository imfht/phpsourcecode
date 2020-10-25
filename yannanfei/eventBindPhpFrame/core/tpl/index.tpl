
/*加入必要函数和类库 可配置化的加载*/
$stime=microtime(true); // 监控时间
lib(array(
'error','function','plugin','control','Blitz' //,'model3','blitzphp'
));


/*引入base插件*/
$plugin_base=plugin('core/base');
$plugin_base->on('init',array(
'core/config->init',
//'core/base->init_session',
//'core/config->config_db',
'core/config->init_act_op',
'core/base->init_timezone',
'core/base->run'
));


$plugin_base->trigger('init');
/*
plugin('resource')->init();
*/

echo ((microtime(true)-$stime)*1000).'ms';