<?php declare (strict_types = 1);
namespace msqphp\core\cli;

use msqphp\Cli;

final class CliFramework
{
    public static function run(): void
    {
        $args = Cli::getCliArgs();
    }

    private static function install(string $root): void
    {
        //根目录
        $root = realpath($root) . DIRECTORY_SEPARATOR;

        //lib目录
        $lib_path = $root . 'library' . DIRECTORY_SEPARATOR . 'msqphp' . DIRECTORY_SEPARATOR . 'framework' . DIRECTORY_SEPARATOR;

        //框架目录
        $framework_path = realpath(__DIR__ . '/../../') . DIRECTORY_SEPARATOR;

        //安装资源目录
        $install_path = $framework_path . DIRECTORY_SEPARATOR . 'resource' . DIRECTORY_SEPARATOR . 'install' . DIRECTORY_SEPARATOR;

        //目录配置
        $path_config = [
            'root'        => $root,
            'application' => $root . 'application',
            'test'        => $root . 'test',
            'resources'   => $root . 'resources',
            'bootstrap'   => $root . 'bootstrap',
            'config'      => $root . 'config',
            'public'      => $root . 'public',
            'storage'     => $root . 'storage',
            'library'     => $lib_path,
        ];
        //复制
        foreach ($path_config as $key => $path) {
            base\dir\Dir::make($path);
            if ('public' === $key || base\dir\Dir::isEmpty($path)) {
                base\dir\Dir::copy($install_path . $key, $path);
            }
            chmod($path, ('public' === $key || 'storage' === $key) ? 0777 : 0755);
        }

        //lib目录对应目录创建
        foreach (base\dir\Dir::getAllDir($framework_path) as $dir) {
            if (base\str\Str::endsWith($dir, ['methods', 'gets', 'staticMethods', 'handlers', 'drivers', 'binds'])) {
                base\dir\Dir::make(str_replace($framework_path . DIRECTORY_SEPARATOR, $lib_path, $dir));
            }
        }
    }
}
