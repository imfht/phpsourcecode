<?php
namespace qeephp\mvc;

/**
 * 与 MVC 有关的异常
 */
class ModuleActionError extends ActionError
{
    const MODULE_NOT_FOUND  = 3;

    static function module_not_found_error($module_name)
    {
        return new static("MODULE_NOT_FOUND: {$module_name}", self::MODULE_NOT_FOUND);
    }
}

