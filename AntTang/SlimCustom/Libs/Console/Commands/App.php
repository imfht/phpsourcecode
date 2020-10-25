<?php
/**
 * @package     App.php
 * @author      Jing <tangjing3321@gmail.com>
 * @link        http://www.slimphp.net
 * @version     1.0
 * @copyright   Copyright (c) SlimCustom.
 * @date        2017年5月31日
 */

namespace SlimCustom\Libs\Console\Commands;

use SlimCustom\Libs\Console\Command;
use SlimCustom\Libs\App as Kernel;

/**
 * App Command
 * 
 * @author Jing <tangjing3321@gmail.com>
 */
class App extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = [
        'app:make',
        'app:remove',
    ];
    
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = [
        'Make a application',
        'Remove a application',
    ];
    
    /**
     * Make a application 
     * 
     * @param string $path
     * @return number|\Clio\false
     */
    public function make($path = null)
    {
        if (Kernel::$instance->deploymentStatus($path)) {
            if (! static::confirm('%y应用已安装，要覆盖吗？%n')) {
                return static::error('%r应用安装取消%n');
            }
        }
        if (! filesystem()->copyDirectory(Kernel::$instance->framerPath() . '/Demo/', Kernel::$instance->path())) {
            return static::error('%r应用安装失败%n');
        }
        $files = filesystem()->allFiles(Kernel::$instance->path())['files'];
        if (! empty($files)) {
            foreach ($files as $file) {
                file_put_contents($file, str_replace('Demo', Kernel::$instance->name(), file_get_contents($file)));
            }
        }
        
        return static::output('%g应用安装成功%n');
    }
    
    /**
     * Remove a application
     * 
     * @param unknown $path
     * @return number|\Clio\false
     */
    public function remove($path = null)
    {
        if (! Kernel::$instance->deploymentStatus($path)) {
            return static::error('%r应用未安装%n');
        }
        
        if (! static::confirm('%y应用已安装，确定卸载吗？%n')) {
            return static::error('%r应用卸载取消%n');
        }
        
        if (! filesystem()->deleteDirectory(Kernel::$instance->path())) {
            return static::error('%r应用卸载失败%n');
        }
        
        return static::output('%g应用卸载成功%n');
    }
}