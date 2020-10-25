<?php
namespace Core;

class Loader
{

    protected $_extensions;

    protected $_namespaces = null;

    protected $_registered = false;

    /**
     * Core\Loader constructor
     */
    public function __construct()
    {
        $this->_extensions = array('php');
    }

    /**
     * Sets an of file $extensions that the loader must try in each attempt to locate the file
     */
    public function setExtensions($extensions)
    {

        $this->_extensions = $extensions;
        return $this;
    }

    /**
     * Returns the file $extensions registered in the loader
     */
    public function getExtensions()
    {
        return $this->_extensions;
    }

    /**
     * Register namespaces and their related directories
     */
    public function registerNamespaces($namespaces = array(), $merge = false)
    {
        if ($merge) {
            $currentNamespaces = $this->_namespaces;
            if (is_array($currentNamespaces)) {
                $mergedNamespaces = array_merge($currentNamespaces, $namespaces);
            } else {
                $mergedNamespaces = $namespaces;
            }
            $this->_namespaces = $mergedNamespaces;
        } else {
            $this->_namespaces = $namespaces;
        }

        return $this;
    }

    /**
     * Returns the namespaces currently registered in the autoloader
     */
    public function getNamespaces()
    {
        return $this->_namespaces;
    }

    /**
     * Register the autoload method
     */
    public function register()
    {
        if ($this->_registered === false) {
            spl_autoload_register(array($this, 'autoload'));
            $this->_registered = true;
        }
        return $this;
    }

    /**
     * Unregister the autoload method
     */
    public function unregister()
    {
        if ($this->_registered === true) {
            spl_autoload_unregister(array($this, 'autoload'));
            $this->_registered = false;
        }
        return $this;
    }

    /**
     * Autoloa$ds the registered classes
     */
    public function autoLoad($className)
    {

        /**
         * Checking in namespaces
         */
        //echo $className;
        if (is_array($this->_namespaces)) {
            if (isset($this->_namespaces[$className]) && file_exists($this->_namespaces[$className])) {
                require $this->_namespaces[$className];
                return true;
            }
        }
        $classNamePath = str_replace('\\', '/', $className);
        //echo $classNamePath . '<br>';
        //$classNamePath = strtolower($classNamePath);
        foreach ($this->_extensions as $extension) {
            $the_path = __DIR__ . '/../' . $classNamePath . '.' . $extension;
            //echo $the_path;
            if (file_exists($the_path)) {
                require $the_path;
                return false;
            }
        }
        /**
         * Cannot find the class, return false
         */
        return false;
    }
}
