<?php

if (!defined('BASEPATH')) exit('No direct script access allowed');

class M_Hooks {


    /**
     * Determines whether hooks are enabled
     *
     * @var	bool
     */
    public $enabled = FALSE;

    /**
     * List of all hooks set in config/hooks.php
     *
     * @var	array
     */
    public $hooks =	array();

    /**
     * Array with class objects to use hooks methods
     *
     * @var array
     */
    protected $_objects = array();

    /**
     * In progress flag
     *
     * Determines whether hook is in progress, used to prevent infinte loops
     *
     * @var	bool
     */
    protected $_in_progress = FALSE;

    /**
     * Class constructor
     *
     * @return	void
     */
    public function __construct()
    {
        $CFG =& load_class('Config', 'core');
        log_message('info', 'Hooks Class Initialized');

        // If hooks are not enabled in the config file
        // there is nothing else to do
        if ($CFG->item('enable_hooks') === FALSE)
        {
            return;
        }

        include(WEBPATH.'config/hooks.php');

        //if (file_exists(APPPATH.'config/'.ENVIRONMENT.'/hooks.php'))
        //{
        //include(APPPATH.'config/'.ENVIRONMENT.'/hooks.php');
        //}

        // If there are no hooks, we're done.
        if ( ! isset($hook) OR ! is_array($hook))
        {
            return;
        }

        $this->hooks =& $hook;
        $this->enabled = TRUE;
    }

    // --------------------------------------------------------------------

    /**
     * Call Hook
     *
     * Calls a particular hook. Called by CodeIgniter.php.
     *
     * @uses	CI_Hooks::_run_hook()
     *
     * @param	string	$which	Hook name
     * @return	bool	TRUE on success or FALSE on failure
     */
    public function call_hook($which = '', $data = '')
    {
        if ( ! $this->enabled OR ! isset($this->hooks[$which]))
        {
            return FALSE;
        }

        if (is_array($this->hooks[$which]) && ! isset($this->hooks[$which]['function']))
        {
            foreach ($this->hooks[$which] as $val)
            {
                $this->_run_hook($val, $data);
            }
        }
        else
        {
            $this->_run_hook($this->hooks[$which], $data);
        }

        return TRUE;
    }

    // --------------------------------------------------------------------

    /**
     * Run Hook
     *
     * Runs a particular hook
     *
     * @param	array	$data	Hook details
     * @return	bool	TRUE on success or FALSE on failure
     */
    protected function _run_hook($data, $params)
    {
        // Closures/lambda functions and array($object, 'method') callables
        if (is_callable($data))
        {
            is_array($data)
                ? $data[0]->{$data[1]}()
                : $data();

            return TRUE;
        }
        elseif ( ! is_array($data))
        {
            return FALSE;
        }

        // -----------------------------------
        // Safety - Prevents run-away loops
        // -----------------------------------

        // If the script being called happens to have the same
        // hook call within it a loop can happen
        if ($this->_in_progress === TRUE)
        {
            return;
        }

        // -----------------------------------
        // Set file path
        // -----------------------------------

        if ( ! isset($data['filepath'], $data['filename']))
        {
            return FALSE;
        }

        $filepath = $data['filepath'].'/'.$data['filename'];

        if ( ! is_file($filepath)) {

            $filepath = FCPATH.'dayrui/'.$data['filepath'].'/'.$data['filename'];

            if ( ! is_file($filepath))
            {
                log_message('error', '钩子文件'.$filepath.'不存在');
                return FALSE;
            }
        }


        // Determine and class and/or function names
        $class		= empty($data['class']) ? FALSE : $data['class'];
        $function	= empty($data['function']) ? FALSE : $data['function'];
        //$params		= isset($data['params']) && $data['params'] ? $data['params'] : '';

        if (empty($function))
        {
            return FALSE;
        }

        // Set the _in_progress flag
        $this->_in_progress = TRUE;

        // Call the requested class and/or function
        if ($class !== FALSE)
        {
            // The object is stored?
            if (isset($this->_objects[$class]))
            {
                if (method_exists($this->_objects[$class], $function))
                {
                    $this->_objects[$class]->$function($params);
                }
                else
                {
                    return $this->_in_progress = FALSE;
                }
            }
            else
            {
                class_exists($class, FALSE) OR require_once($filepath);

                if ( ! class_exists($class, FALSE) OR ! method_exists($class, $function))
                {
                    log_message('error', '钩子文件'.$filepath.' ： '.$class.'/'.$function.' 不存在');
                    return $this->_in_progress = FALSE;
                }

                // Store the object and execute the method
                $this->_objects[$class] = new $class();
                $this->_objects[$class]->$function($params);
            }
        }
        else
        {
            function_exists($function) OR require_once($filepath);

            if ( ! function_exists($function))
            {
                return $this->_in_progress = FALSE;
            }

            $function($params);
        }

        $this->_in_progress = FALSE;
        return TRUE;
    }
}