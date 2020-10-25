<?php declare(strict_types = 1);
namespace msqphp\core\route;

trait RouteCategoryTrait
{
    private static $category_info = [];

    // 得到当前的分组信息
    public static function getGroupInfo() : array
    {
        return static::$category_info['group'];
    }

    /**
     * 得到允许值,用于addLanguage,addTheme,addGroup
     * 如果非默认取值则添加值当前url参数
     * 定义对应常量
     *
     * @param  array  $info 信息['allowed'=>string(路由规则)|array(指定值),'default',默认值];
     *
     * @return string
     */
    private static function getAndAddAllowedCategoryValue(string $name, $allowed, $default) : string
    {
        // 如果当前url参数仍有值 并且 检测成功
        if (isset(static::$pending_path[0]) && static::checkAllowedCategoryValue(static::$pending_path[0], $allowed)) {
            // 取值
            $result = array_shift(static::$pending_path);
            // 追加至Url
            static::$url .= $result.'/';
        } else {
            if (is_string($default)) {
                // 取默认
                $result = $default;
            } elseif ($default instanceof \Closure) {
                $result = (string) call_user_func_array($default, []);
            } else {
                throw new RouteException('错误的分组默认值类型,支持字符串定值或者一个返回字符串值的闭包函数');
            }
        }
        // 添加对应常量
        static::addCategoryConstant($name, $result);
        return $result;
    }

    // 添加一个分类常量
    private static function addCategoryConstant(string $name, string $value) : void
    {
        // 定义常量并存储到分组信息中
        $constant = '__'.strtoupper($name).'__';
        defined($constant) || define($constant, $value);
        static::$category_info['constant'][$constant] = $value;
    }

    /**
     * 分类允许值检测
     *
     * @param  string $may   可能值
     * @param  miexd  $value 指定值
     *
     * @return bool
     */
    private static function checkAllowedCategoryValue(string $may, $value) : bool
    {
        // 如果是个字符串,则检测规则
        if (is_string($value)) {
            return static::checkRoule($may, $value);
        } elseif (is_array($value)) {
        // 如果是个数组, 判断是否是数组中的某个值
            return in_array($may, $value);
        } else {
            static::exception($may.'未知的检测类型,检测类型应为数组或路由规则名称');
        }
    }

    public static function setGroup(string $group, string $value, ?string $namespace = null) : void
    {
        static::$category_info['group'][] = static::$category_info['group'][$group] = $value;
        static::addCategoryConstant($group, $value);

        if ($namespace != null) {
            static::$namespace .= trim($value, '\\').'\\';
        }
    }
    public static function setLanguage(string $language) : void
    {
        static::$category_info['language'] = $language;
        static::addCategoryConstant('language', $language);
    }
    public static function setTheme(string $theme) : void
    {
        static::$category_info['theme'] = $theme;
        static::addCategoryConstant('theme', $theme);
    }

    /**
     * 多语支持 || 多主题支持
     * @param   array      $info = [
     *     'allowed'   => string(路由规则)  |  array(允许值一维数组),
     *     'default'   => string(默认值),
     * ];
     */
    public static function addLanguage(array $info) : void
    {
        static::$category_info['language'] = static::getAndAddAllowedCategoryValue('language', $info['allowed'], $info['default']);
    }
    public static function addTheme(array $info) : void
    {
        static::$category_info['theme'] = static::getAndAddAllowedCategoryValue('theme', $info['allowed'], $info['default']);
    }

    /**
     * 增加一个url分组信息
     * 将获取待处理路径第一个参数
     * 如果有且允许,则取值并从移除
     * 否则取默认值
     *
     * @param  array $info 分组信息
     * 关联数组;
     * $info = [
     *    'name'      =>string(组名)'module',
     *    'allowed'   =>string(路由规则) || array(允许值一维数组)
     *    'default'   =>string('组默认值')
     *    ['namespace' =>true(与组值相同) || string('固定值') ]
     *    (可选,将在当前namespace基础上添加一个命名空间,基础值为app);
     * ];
     *
     * @return void
     */
    public static function addGroup(array $info) : void
    {
        // 赋值给当前信息和分组, 键为组名, 值: 如果在允许范围内, 取其值, 否则取默认;
        static::$category_info['group'][] = static::$category_info['group'][$info['name']] = $group = static::getAndAddAllowedCategoryValue($info['name'], $info['allowed'], $info['default']);

        if ($info['namespace'] !== null) {
            // bool等于组值
            if (true === $info['namespace']) {
                static::$namespace .= trim($group, '\\').'\\';
            } elseif (is_string($namespace)) {
            // 否则为过固定值
                static::$namespace .= trim($info['namespace'], '\\').'\\';
            } else {
                static::exception('路由分组的命名空间类型未知,应为true(与组值相同)或者string(固定值');
            }
        }
    }
}