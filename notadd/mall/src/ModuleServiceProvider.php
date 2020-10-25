<?php
/**
 * This file is part of Notadd.
 *
 * @author Qiyueshiyi <qiyueshiyi@outlook.com>
 * @copyright (c) 2017, notadd.com
 * @datetime 2017-03-22 16:02
 */
namespace Notadd\Mall;

use Notadd\Foundation\Module\Abstracts\Module;

/**
 * Class ModuleServiceProvider.
 */
class ModuleServiceProvider extends Module
{
    /**
     * Boot module.
     */
    public function boot()
    {
        $this->loadTranslationsFrom(realpath(__DIR__ . '/../resources/translations'), 'mall');
        $this->loadViewsFrom(realpath(__DIR__ . '/../resources/views'), 'mall');
    }
}
