<?php declare (strict_types = 1);
namespace msqphp\core\loader;

use msqphp\base;
use msqphp\base\file\File;

/**
 * 实现原理:
 * 修改代码 composer/ClassLoader.php函数为
 * function includeFile($file)
 * {
 *   \msqphp\core\loader\Loader::addClasses($file);
 *   include $file;
 * }
 * 或者使用框架本身加载类
 * 处理过程: 未知->收集->整理->完成
 * +. 判断是否有缓存信息,有则判断对应状态,跳至对应步骤
 * +. 未知:没有对应信息,在结束时获得一次加载文件列表,进入收集模式
 * +. 收集:判断收集记录个数->不足继续收集
 *                         ->整理所有记录,当出现概率超过70%时,将该文件放入待整理文件列表中.
 * +. 整理:整理文件列表,直至依赖关系解决,排序完成
 * +. 完成:直接载入
 * [
 *     'type'    => 'unknown|collect|miexd|last',
 *     'collect' => [] // 收集记录,
 *     'needful' => [] // 需要文件
 *     'tidied'  => [] // 整理后文件
 *     'last'    => [] // 最终列表
 * ]
 *
 * 例:
 * 需要加载123456789九个文件, 1需要23,2需要468,3需要57,45678无依赖
 * 1. 收集12345678
 * 2. 收集123456789
 * .....
 * 10. 收集结果12345678十次,9五次,需要加载文件列表为12345678
 * 11. 加载文件1,加载前未加载.放入tided,但依赖导致加载2345678, 当加载2-8时,时光荏加载前已经加载,放至needful中
 * 12. 将2-8反序加载,依次加载文件8765432,加载前均未加载,放入tidied中,此时,needful为空获得最终加载文件列表
 * 13. 直接加载87654321.
 */
final class AILoader
{
    private static function exception(string $message): void
    {
        throw new AILoaderException($message);
    }
    private static function getCacheDirPath(): string
    {
        return \msqphp\Environment::getPath('storage') . 'framework/aiload/';
    }

    public static function useAiload(string $key, int $failure_rate, \Closure $func, array $args = [],  ? string $path = null, bool $clear_all = false)
    {
        $scope_n = AutoLoadRecord::getScope();
        AutoLoadRecord::setScope($key);

        $cache_dir_path = static::getCacheDirPath();

        $aiload_last_file = $path ?? $cache_dir_path . $key . '.php';
        HAS_CACHE || File::delete($aiload_last_file);

        if (is_file($aiload_last_file)) {
            include $aiload_last_file;
            call_user_func_array($func, $args);
            if (random_int(1, $failure_rate) === 1) {
                File::delete($aiload_last_file);
                $clear_all && static::clearAll();
            }
        } else {
            $aiload_middle_info_file = $cache_dir_path . md5($key) . '.php';
            HAS_CACHE || File::delete($aiload_middle_info_file);
            $info = is_file($aiload_middle_info_file) ? require $aiload_middle_info_file : ['type' => 'unknown'];
            $info = static::tidyInfo($info);

            //初始化
            call_user_func_array($func, $args);

            // 如果为最终,初始化,删除
            if ($info['type'] === 'last') {
                File::write($aiload_last_file, empty($info['last']) ? ''
                    : '<?php include \'' . implode('\';include \'', $info['last']) . '\';'
                    , true);
                File::delete($aiload_middle_info_file);
            } else {
                $clear_all && static::clearAll();
                $info = static::updateInfo($info);
                // 写入缓存
                File::write($aiload_middle_info_file, '<?php return ' . var_export($info, true) . ';');
            }
        }

        AutoLoadRecord::setScope($scope_n);
    }
    private static function tidyInfo(array $info) : array
    {
        switch ($info['type']) {
            case 'unknown':
            case 'collect':
                break;
            case 'mixed':
                // 需要加载 整理后的列表
                $needful = $tidied = [];
                // 加载需要加载的文件
                foreach (array_reverse($info['needful']) as $file) {
                    // 如果已经加载过,再次放入needful
                    if (in_array($file, AutoLoadRecord::getAllLoadedClasses())) {
                        $needful[] = $file;
                    } else {
                        // 添加至整理过的
                        $tidied[] = $file;
                        require $file;
                    }
                }
                // 加载所有整理过的文件
                if (isset($info['tidied'])) {
                    foreach ($info['tidied'] as $file) {
                        require $file;
                        $tidied[] = $file;
                    }
                }
                // 如果needful为空,则表示得到最终加载顺序
                $info = empty($needful) ? ['type' => 'last', 'last' => $tidied] : ['type' => 'mixed', 'needful' => $needful, 'tidied' => $tidied];
                break;
            default:
                static::exception('错误的aiload缓存,文件位置:' . (string) $file);
        }
        return $info;
    }
    private static function updateInfo(array $info): array
    {

        switch ($info['type']) {
            case 'unknown':
                // 没有数据,获得第一的的记录,进入收集模式
                $info = ['type' => 'collect', 'collect' => [AutoLoadRecord::getClassesInCurrnetScope()]];
                break;
            case 'collect':
                $collect = $info['collect'];
                // 如果收集结果等于10,开始整理收集记录
                if (10 === count($collect)) {
                    $needful = [];

                    $counts = [];

                    foreach ($collect as $collect_files) {
                        foreach ($collect_files as $collect_file) {
                            $counts[$collect_file] = $counts[$collect_file] ?? 0;
                            ++$counts[$collect_file];
                        }
                    }

                    foreach ($counts as $file => $count) {
                        $count > 7 && $needful[] = $file;
                    }

                    // 得到混合文件列表,排序未知
                    $info = ['type' => 'mixed', 'needful' => $needful];
                } else {
                    // 继续收集
                    $info = ['type' => 'collect', 'collect' => array_merge($collect, [AutoLoadRecord::getClassesInCurrnetScope()])];
                }
                break;
            case 'mixed':
            case 'last':
                break;
            default:
                static::exception('未知错误');
        }
        return $info;
    }
    // 删除所有缓存
    public static function clearAll(): void
    {
        try {
            // 清空对应目录下所有文件
            base\dir\Dir::empty(static::getCacheDirPath());
        } catch (base\dir\DirException $e) {
            static::exception('无法删除所有智能加载缓存文件,错误原因:' . (string) $e->getMessage());
        }
    }
}
