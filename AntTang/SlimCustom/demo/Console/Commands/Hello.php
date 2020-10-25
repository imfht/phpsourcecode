<?php
/**
 * @package     Hello.php
 * @author      Jing <tangjing3321@gmail.com>
 * @link        http://www.slimphp.net
 * @version     1.0
 * @copyright   Copyright (c) SlimCustom.
 * @date        2017年5月31日
 */

namespace Demo\Console\Commands;

use SlimCustom\Libs\Console\Command;

/**
 * Hello Command
 * 
 * @author Jing <tangjing3321@gmail.com>
 */
class Hello extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = [
        'demo:hello',
    ];
    
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = [
        'Demo command for application ',
    ];
    
    /**
     * Say hello
     * 
     * @param string $path
     * @return number|\Clio\false
     */
    public function hello($path = null)
    {
        return static::output('%ghello%n');
    }
}