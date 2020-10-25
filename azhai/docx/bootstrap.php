<?php
error_reporting(E_ALL & ~E_DEPRECATED);
defined('PROJECT_DIR') or define('PROJECT_DIR', __DIR__);
defined('VENDOR_DIR') or define('VENDOR_DIR', PROJECT_DIR . '/vendor');
defined('DS') or define('DS', DIRECTORY_SEPARATOR);
if (version_compare(PHP_VERSION, '5.4.0') < 0) {
    die('PHP最低要求5.4版本');
}

$package_file = VENDOR_DIR . '/docx.lite.php';
if (is_readable($package_file)) { // 使用压缩后的文件
    require_once $package_file;
} else {
    require_once VENDOR_DIR . '/Docx/Importer.php';
}
$importer = \Docx\Importer::getInstance();

$config_file = APP_DIR . '/public/archives/configs.php';
$configs = [
    'debug' => true,
    'route_key' => 'r',
    #相对路径，自动加上APP_DIR前缀
    'public_dir' => 'public',               #静态输出目录
    'archives_dir' => 'archives',           #原始文档目录
    'assets_dir' => 'assets',               #资源目录
    'title' => 'docx文档生成工具',
    'tagline' => '最简单的方式构建你的项目文档',
    'reading' => '开始阅读文档',
    'cover_image' => 'img/cover.png',
    'author' => '',
    'layout' => 'post',                     #默认模板布局
    'date_format' => 'Y年n月j日 星期w',
    'timezone' => 'Asia/Shanghai',
    'memory_limit' => '256M',
    #相对路径，自动加上PROJECT_DIR前缀
    'theme_dir' => 'theme',                 #主题模板目录
    'cache_dir' => 'temp',                  #缓存目录
    'cache_ext' => '.json',
    #pages仓库
    'repo_url' => '',
    'repo_user' => '',
    'repo_pass' => '',
    'repo_branch' => 'coding-pages',
    #github仓库名称
    'github_repo_name' => 'azhai/docx',
    'google_analytics' => false,
    'links' => [
        'Coding仓库' => 'https://coding.net/u/azhai/p/docx/git',
        'OSChina仓库' => 'http://git.oschina.net/azhai/docx',
        'Todaymade出品Daux.io' => 'http://todaymade.com',
        'Xin Meng翻译文档' => 'http://blog.emx2.co.uk',
    ],
    'greetings' => [
        '在合适的时候使用PHP – Rasmus Lerdorf',
        '使用多表存储提高规模伸缩性 – Matt Mullenweg',
        '千万不要相信用户 – Dave Child',
        '多使用PHP缓存 – Ben Balbo',
        '使用IDE, Templates和Snippets加速PHP开发 – Chad Kieffer',
        '利用好PHP的过滤函数 – Joey Sochacki',
        '使用PHP框架 – Josh Sharp',
        '不要使用PHP框架 – Rasmus Lerdorf',
        '使用批处理 – Jack D. Herrington',
        '及时启用错误报告 – David Cummings',
    ],
];
if (is_readable($config_file)) {
    if ($custom_configs = include $config_file) {
        $configs = array_merge($configs, $custom_configs);
    }
}

$app = new \Docx\Base\Application([
    'configs' => $configs,
    '\\Docx\\Web\\URL' => [
        'default' => ['route_key' => $configs['route_key']],
    ],
]);
$repo_config = [
    'static_method' => 'createInstance',
    'local_dir' => APP_DIR . DS . $configs['public_dir'],
    'git' => null,
    'repo_args' => [
        'url' => $configs['repo_url'],
        'user' => $configs['repo_user'],
        'pass' => $configs['repo_pass'],
    ],
];
$app->install('repo', '\\Docx\\Utility\\Repository', $repo_config);


require_once PROJECT_DIR . '/handlers.php';
require_once PROJECT_DIR . '/helpers.php';
$app->route('/<path>', 'Viewhandler');
$app->route('/admin/<path>', 'EditHandler');
$app->route('/admin/staticize/', 'HtmlHandler');
$app->route('/admin/publish/', 'RepoHandler');

$app->route('/admin/compress/', function() use($importer, $package_file) {
    if (!\Docx\Common::isCLI()) {
        return "请在命令行下执行这个操作！\n";
    }
    $importer->addClass(VENDOR_DIR . '/Docx/Compressor.php', '\\Docx\\Compressor');
    $cps = new \Docx\Compressor();
    $cps->minify($package_file,
                glob(VENDOR_DIR . '/Docx/Common.php'),
                glob(VENDOR_DIR . '/Docx/Importer.php'),
                glob(VENDOR_DIR . '/Docx/Base/*.php'),
                glob(VENDOR_DIR . '/Docx/Event/*.php'),
                glob(VENDOR_DIR . '/Docx/*/*.php'));
    return "DONE.\n";
});
$app->route('/admin/deploy/<word>', function($name = 'blog') {
    if (!\Docx\Common::isCLI()) {
        return "请在命令行下执行这个操作！\n";
    }
    $target_dir = dirname(APP_DIR) . '/' . $name;
    if (file_exists($target_dir)) {
        return "The directory named $target_dir is EXISTS. Change another name!\n";
    }
    mkdir($target_dir);
    $project_dir = realpath(PROJECT_DIR);
    system('cp -r public/ ' . $target_dir . '/public');
    $content = <<<EOD
defined('APP_DIR') or define('APP_DIR', __DIR__);
defined('PROJECT_DIR') or define('PROJECT_DIR', $project_dir);
include PROJECT_DIR . '/bootstrap.php';
EOD;
    file_put_contents($target_dir . '/index.php', '<' . '?php' . $content);
    return "DONE.\n";
});

$app->run();
?>