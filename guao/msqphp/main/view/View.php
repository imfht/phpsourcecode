<?php declare (strict_types = 1);
namespace msqphp\main\view;

use msqphp\base;
use msqphp\core;
use msqphp\main;

abstract class View
{
    // 当前配置
    protected $config = [];
    // 当前组件
    protected $tpl         = [];
    protected $data        = null;
    protected $theme       = null;
    protected $group       = null;
    protected $static_html = null;
    protected $language    = null;

    // 抛出异常
    private function exception(string $message): void
    {
        throw new ViewException('[视图异常]' . $message);
    }

    // 构造函数
    public function __construct()
    {
        // 初始化配置
        $this->initConfig();
        // 获得一个视图数据对象
        $this->data = new component\Data();
        // 获得一个视图分组对象
        $this->group = new component\Group();
        // 如果支持多主题,获得一个视图主题对象
        $this->config['multiple_theme'] && $this->theme = new component\Theme($this->config['theme_config']);
        // 如果支持多语言,获得一个视图语言对象
        $this->config['multilingual'] && $this->language = new component\Language($this->config['language_config']);
    }

    // 配置初始化
    private function initConfig(): void
    {
        // 载入配置
        $config = core\config\Config::get('view');

        // 目录检测
        is_dir($config['tpl_material_path']) || $this->exception('模版原料路径不存在');
        is_dir($config['tpl_part_path']) || $this->exception('模版零件缓存路径不存在');
        is_dir($config['tpl_package_path']) || $this->exception('模版组件缓存路径不存在');

        // 重新赋值
        $config['tpl_material_path'] = realpath($config['tpl_material_path']) . DIRECTORY_SEPARATOR;
        $config['tpl_part_path']     = realpath($config['tpl_part_path']) . DIRECTORY_SEPARATOR;
        $config['tpl_package_path']  = realpath($config['tpl_package_path']) . DIRECTORY_SEPARATOR;

        // 赋值
        $this->config = $config;
    }

    /**
     * 得到视图文件路径
     * 基础路径/[route分组][语言][主题]文件名[后缀]
     *
     * @param   string  $name  名称
     * @param   string  $type  类型
     *
     * @return  string
     */
    private function getTplFilePath(string $name, string $type): string
    {
        // 若第一个字符为/ || \,则为顶级分组, 否则取路由对应分组值
        $group = $name[0] === '/' || $name[0] === '\\' ? '' : $this->group->get();
        // 主题,语言存在取值
        $theme    = $this->theme === null ? '' : $this->theme->get() . DIRECTORY_SEPARATOR;
        $language = $this->language === null ? '' : $this->language->get() . DIRECTORY_SEPARATOR;

        $middle = $theme . $group . $name;

        switch ($type) {
            case 'material':
                $file = $this->config['tpl_material_path'] . $middle . $this->config['tpl_material_ext'];
                return is_file($file) ? $file : $this->config['tpl_material_path'] . $middle . $this->config['tpl_material_ext'];
            case 'part':
                return $this->config['tpl_part_path'] . $language . $middle . $this->config['tpl_part_ext'];
            case 'package':
                return $this->config['tpl_package_path'] . $language . $middle . $this->config['tpl_package_ext'];
            default:
                $this->exception('获取模版路径错误,未知的模版类型:' . $type);
        }
    }

    /**
     * @param  string $material       原料名
     * @param  string $part
     * @param  string $part_name      零件名称
     * @param  string $package
     * @param  string $package_name   组件名
     * @param  int    $expire         过期时间
     */

    // 当前页面为静态页面
    protected function staticHtml(int $expire = 3600): self
    {
        // 允许静态则生成一个视图静态页面对象
        HAS_STATIC && $this->static_html = new component\StaticHtml(['expire' => $expire]);
        return $this;
    }

    // 添加一个原料
    protected function material(string $material): self
    {
        return $this->addTpl($material, 'material');
    }

    // 添加一个零件
    protected function part(string $part): self
    {
        return $this->addTpl($part, 'part');
    }
    protected function parts(array $parts): self
    {
        foreach ($parts as $part) {
            $this->part($part);
        }
        return $this;
    }
    // 添加一个组件
    protected function package(string $package): self
    {
        return $this->addTpl($package, 'package');
    }
    // 添加一个视图
    private function addTpl(string $name, string $type): self
    {
        $this->tpl[] = ['type' => $type, 'name' => $name];
        return $this;
    }
    // 加工一个原件 material->part
    protected function process(string $part_name, int $expire = 7200): void
    {
        // 原材料信息
        $material_info = array_pop($this->tpl);
        // 是否为原材料
        'material' === $material_info['type'] || $this->exception('模版文件无法加工,原因:当前视图组件中最后一个不为原料视图');
        // 获得并检查文件是否存在
        $material_file = $this->getTplFilePath($material_info['name'], 'material');
        is_file($material_file) || $this->exception('模版文件无法加工,原因:' . $material_info['name'] . '模版不存在,模版文件位置应为' . $material_file);
        // 获得编译后零件信息
        $part_file = $this->getTplFilePath($part_name, 'part');
        $now       = time();
        // 开始头,表明过期时间
        $begin = '<?php /*' . (string) $now . (0 !== $expire ? (string) ($now + $expire) : '0000000000') . '*/?>';
        // 写入对应信息
        base\file\File::write($part_file, base\str\Str::formatHtml($begin . main\template\Template::commpileFile(
            // 文件内容
            $material_file,
            // 模版数据
            $this->data->getAll(),
            // 语言数据
            $this->language === null ? $this->language->getData($part_name, $this->group->get()) : []
        )));
    }
    protected function getCreateTime(string $name, string $type)
    {
        $file = $this->getTplFilePath($name, $type);
        return (int) substr(base\file\File::read($file, 31), 8, 10);
    }
    private function isExpired(string $file): bool
    {
        if (HAS_VIEW && is_file($file)) {
            if (time() > (int) substr(base\file\File::read($file, 31), 18, 10)) {
                base\file\File::delete($file);
                return false;
            }
            return true;
        }
        return false;
    }
    // 原材料是否加工过,也可以理解为零件是否存在
    protected function processed(string $part_name): bool
    {
        return $this->isExpired($this->getTplFilePath($part_name, 'part'));

    }
    // 拼装 part->package
    protected function assemble(string $package_name, int $expire = 3600): void
    {
        $material = $this->getAllComponnt();

        $package_file = $this->getTplFilePath($package_name, 'package');

        $now = time();

        $result = '<?php /*' . (string) $now . (0 !== $expire ? (string) ($now + $expire) : '0000000000') . '*/?>';

        foreach ($material as $file) {
            $result .= substr(base\file\File::get($file), 32);
        }

        base\file\File::write($package_file, base\str\Str::formatHtml($result), true);

        unset($result);

        $this->tpl = [];
    }

    // 是否拼装过,也可以理解为组件是否存在
    protected function assembled(string $package_name): bool
    {
        return $this->isExpired($this->getTplFilePath($package_name, 'package'));
    }

    // 展示
    protected function show(): void
    {
        if ($this->static_html === null) {
            core\response\Response::htmlFiles($this->getAllComponnt(), $this->data->getKeyValueData(), false, false);
        } else {
            $this->static_html->addContent(core\response\Response::htmlFiles($this->getAllComponnt(), $this->data->getKeyValueData(), false, true));
        }
    }

    private function getAllComponnt(): array
    {
        $result = [];
        foreach ($this->tpl as ['type' => $type, 'name' => $name]) {
            // 获得对应文件路径
            $file = $this->getTplFilePath($name, $type);
            // 如果是零件,  文件不存在或者无视图缓存重新加工
            if ('part' === $type && (!is_file($file) || !HAS_VIEW)) {
                $this->material($name)->process($name);
            }
            // 如果文件不存在
            is_file($file) || $this->exception('视图' . ($type === 'part' ? '零件' : '组件') . (string) $name . '不存在,文件路径为' . $file);
            // 添加到结果数组中
            $result[] = $file;
        }
        return $result;
    }
}
