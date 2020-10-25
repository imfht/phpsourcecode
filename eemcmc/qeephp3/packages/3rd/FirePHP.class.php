<?php
if (!defined('E_STRICT')) {
    define('E_STRICT', 2048);
}
if (!defined('E_RECOVERABLE_ERROR')) {
    define('E_RECOVERABLE_ERROR', 4096);
}
if (!defined('E_DEPRECATED')) {
    define('E_DEPRECATED', 8192);
}
if (!defined('E_USER_DEPRECATED')) {
    define('E_USER_DEPRECATED', 16384);
} 

class FirePHP {

    const VERSION = '0.3';
    const LOG = 'LOG';  
    const INFO = 'INFO';
    const WARN = 'WARN';
    const ERROR = 'ERROR';
    const DUMP = 'DUMP';
    const TRACE = 'TRACE';
    const EXCEPTION = 'EXCEPTION';
    
    const TABLE = 'TABLE';
    const GROUP_START = 'GROUP_START';
    const GROUP_END = 'GROUP_END';
    
    protected static $instance = null;
    
    protected $inExceptionHandler = false;
        
    protected $throwErrorExceptions = true;
    
    protected $convertAssertionErrorsToExceptions = true;
        
    protected $throwAssertionExceptions = false;

    protected $messageIndex = 1;
    
    protected $options = array('maxDepth' => 10,
                               'maxObjectDepth' => 5,
                               'maxArrayDepth' => 5,
                               'useNativeJsonEncode' => true,
                               'includeLineNumbers' => true);

    
    protected $objectFilters = array(
        'firephp' => array('objectStack', 'instance', 'json_objectStack'),
        'firephp_test_class' => array('objectStack', 'instance', 'json_objectStack')
    );
    
    protected $objectStack = array();

    protected $enabled = true;

    protected $logToInsightConsole = null;

    function __sleep()
    {
        return array('options','objectFilters','enabled');
    }
    
    /**
     * Gets singleton instance of FirePHP
     *
     * @param boolean $AutoCreate
     * @return FirePHP
     */
    static function getInstance($AutoCreate = false)
    {
        if ($AutoCreate===true && !self::$instance) {
            self::init();
        }
        return self::$instance;
    }
    
    static function init()
    {
        return self::setInstance(new self());
    }

    static function setInstance($instance)
    {
        return self::$instance = $instance;
    }
    
    function setLogToInsightConsole($console)
    {
        if(is_string($console)) {
            if(get_class($this)!='FirePHP_Insight' && !is_subclass_of($this, 'FirePHP_Insight')) {
                throw new Exception('FirePHP instance not an instance or subclass of FirePHP_Insight!');
            }
            $this->logToInsightConsole = $this->to('request')->console($console);
        } else {
            $this->logToInsightConsole = $console;
        }
    }

    function setEnabled($Enabled)
    {
       $this->enabled = $Enabled;
    }
    
    function getEnabled()
    {
        return $this->enabled;
    }    
    
    function setObjectFilter($Class, $Filter)
    {
        $this->objectFilters[strtolower($Class)] = $Filter;
    }
  
    /**
     * Set some options for the library
     * 
     * Options:
     *  - maxDepth: The maximum depth to traverse (default: 10)
     *  - maxObjectDepth: The maximum depth to traverse objects (default: 5)
     *  - maxArrayDepth: The maximum depth to traverse arrays (default: 5)
     *  - useNativeJsonEncode: If true will use json_encode() (default: true)
     *  - includeLineNumbers: If true will include line numbers and filenames (default: true)
     * 
     * @param array $Options The options to be set
     * @return void
     */
    function setOptions($Options)
    {
        $this->options = array_merge($this->options,$Options);
    }
    
    function getOptions()
    {
        return $this->options;
    }

    function setOption($Name, $Value)
    {
        if (!isset($this->options[$Name])) {
            throw $this->newException('Unknown option: ' . $Name);
        }
        $this->options[$Name] = $Value;
    }

    function getOption($Name)
    {
        if (!isset($this->options[$Name])) {
            throw $this->newException('Unknown option: ' . $Name);
        }
        return $this->options[$Name];
    }

    function registerErrorHandler($throwErrorExceptions = false)
    {        
        $this->throwErrorExceptions = $throwErrorExceptions;
    
        return set_error_handler(array($this,'errorHandler'));     
    }

    function errorHandler($errno, $errstr, $errfile, $errline, $errcontext)
    {
        if (error_reporting() == 0) {
            return;
        }
        if (error_reporting() & $errno) {

            $exception = new ErrorException($errstr, 0, $errno, $errfile, $errline);
            if ($this->throwErrorExceptions) {
                throw $exception;
            } else {
                $this->fb($exception);
            }
        }
    }
  
    function registerExceptionHandler()
    {
        return set_exception_handler(array($this,'exceptionHandler'));     
    }
  
    function exceptionHandler($Exception)
    {
    
        $this->inExceptionHandler = true;
    
        header('HTTP/1.1 500 Internal Server Error');
    
        try {
            $this->fb($Exception);
        } catch (Exception $e) {
            echo 'We had an exception: ' . $e;
        }
        $this->inExceptionHandler = false;
    }  
    
    function registerAssertionHandler($convertAssertionErrorsToExceptions = true, $throwAssertionExceptions = false)
    {
        $this->convertAssertionErrorsToExceptions = $convertAssertionErrorsToExceptions;
        $this->throwAssertionExceptions = $throwAssertionExceptions;
        
        if ($throwAssertionExceptions && !$convertAssertionErrorsToExceptions) {
            throw $this->newException('Cannot throw assertion exceptions as assertion errors are not being converted to exceptions!');
        }
        
        return assert_options(ASSERT_CALLBACK, array($this, 'assertionHandler'));
    }
  
    function assertionHandler($file, $line, $code)
    {
        if ($this->convertAssertionErrorsToExceptions) {
          
          $exception = new ErrorException('Assertion Failed - Code[ '.$code.' ]', 0, null, $file, $line);
    
          if ($this->throwAssertionExceptions) {
              throw $exception;
          } else {
              $this->fb($exception);
          }
        
        } else {
            $this->fb($code, 'Assertion Failed', FirePHP::ERROR, array('File'=>$file,'Line'=>$line));
        }
    }
  
    function group($Name, $Options = null)
    {
    
        if (!$Name) {
            throw $this->newException('You must specify a label for the group!');
        }
        
        if ($Options) {
            if (!is_array($Options)) {
                throw $this->newException('Options must be defined as an array!');
            }
            if (array_key_exists('Collapsed', $Options)) {
                $Options['Collapsed'] = ($Options['Collapsed'])?'true':'false';
            }
        }
        
        return $this->fb(null, $Name, FirePHP::GROUP_START, $Options);
    }
  
    function groupEnd()
    {
        return $this->fb(null, null, FirePHP::GROUP_END);
    }

    function log($Object, $Label = null, $Options = array())
    {
        return $this->fb($Object, $Label, FirePHP::LOG, $Options);
    } 

    function info($Object, $Label = null, $Options = array())
    {
        return $this->fb($Object, $Label, FirePHP::INFO, $Options);
    } 
   
    function warn($Object, $Label = null, $Options = array())
    {
        return $this->fb($Object, $Label, FirePHP::WARN, $Options);
    } 
    
    function error($Object, $Label = null, $Options = array())
    {
        return $this->fb($Object, $Label, FirePHP::ERROR, $Options);
    } 

    function dump($Key, $Variable, $Options = array())
    {
        if (!is_string($Key)) {
            throw $this->newException('Key passed to dump() is not a string');
        }
        if (strlen($Key)>100) {
            throw $this->newException('Key passed to dump() is longer than 100 characters');
        }
        if (!preg_match_all('/^[a-zA-Z0-9-_\.:]*$/', $Key, $m)) {
            throw $this->newException('Key passed to dump() contains invalid characters [a-zA-Z0-9-_\.:]');
        }
        return $this->fb($Variable, $Key, FirePHP::DUMP, $Options);
    }
  
    function trace($Label)
    {
        return $this->fb($Label, FirePHP::TRACE);
    } 

    function table($Label, $Table, $Options = array())
    {
        return $this->fb($Table, $Label, FirePHP::TABLE, $Options);
    }

    static function to()
    {
        $instance = self::getInstance();
        if (!method_exists($instance, "_to")) {
            throw new Exception("FirePHP::to() implementation not loaded");
        }
        $args = func_get_args();
        return call_user_func_array(array($instance, '_to'), $args);
    }

    static function plugin()
    {
        $instance = self::getInstance();
        if (!method_exists($instance, "_plugin")) {
            throw new Exception("FirePHP::plugin() implementation not loaded");
        }
        $args = func_get_args();
        return call_user_func_array(array($instance, '_plugin'), $args);
    }

    function detectClientExtension()
    {
        if (@preg_match_all('/\sFirePHP\/([\.\d]*)\s?/si',$this->getUserAgent(),$m) &&
           version_compare($m[1][0],'0.0.6','>=')) {
            return true;
        } else
        if (@preg_match_all('/^([\.\d]*)$/si',$this->getRequestHeader("X-FirePHP-Version"),$m) &&
           version_compare($m[1][0],'0.0.6','>=')) {
            return true;
        }
        return false;
    }
 
    function fb($Object)
    {
        if($this instanceof FirePHP_Insight && method_exists($this, '_logUpgradeClientMessage')) {
            if(!FirePHP_Insight::$upgradeClientMessageLogged) {
                $this->_logUpgradeClientMessage();
            }
        }

        static $insightGroupStack = array();

        if (!$this->getEnabled()) {
            return false;
        }

        if ($this->headersSent($filename, $linenum)) {
            
            if ($this->inExceptionHandler) {
                
                echo '<div style="border: 2px solid red; font-family: Arial; font-size: 12px; background-color: lightgray; padding: 5px;"><span style="color: red; font-weight: bold;">FirePHP ERROR:</span> Headers already sent in <b>'.$filename.'</b> on line <b>'.$linenum.'</b>. Cannot send log data to FirePHP. You must have Output Buffering enabled via ob_start() or output_buffering ini directive.</div>';
            } else {
                throw $this->newException('Headers already sent in '.$filename.' on line '.$linenum.'. Cannot send log data to FirePHP. You must have Output Buffering enabled via ob_start() or output_buffering ini directive.');
            }
        }
      
        $Type = null;
        $Label = null;
        $Options = array();
      
        if (func_num_args()==1) {
        } else if (func_num_args()==2) {
            switch(func_get_arg(1)) {
                case self::LOG:
                case self::INFO:
                case self::WARN:
                case self::ERROR:
                case self::DUMP:
                case self::TRACE:
                case self::EXCEPTION:
                case self::TABLE:
                case self::GROUP_START:
                case self::GROUP_END:
                    $Type = func_get_arg(1);
                    break;
                default:
                    $Label = func_get_arg(1);
                    break;
            }
        } else if (func_num_args()==3) {
            $Type = func_get_arg(2);
            $Label = func_get_arg(1);
        } else if (func_num_args()==4) {
            $Type = func_get_arg(2);
            $Label = func_get_arg(1);
            $Options = func_get_arg(3);
        } else {
            throw $this->newException('Wrong number of arguments to fb() function!');
        }

        if($this->logToInsightConsole!==null && (get_class($this)=='FirePHP_Insight' || is_subclass_of($this, 'FirePHP_Insight'))) {
            $msg = $this->logToInsightConsole;
            if ($Object instanceof Exception) {
                $Type = self::EXCEPTION;
            }
            if($Label && $Type!=self::TABLE && $Type!=self::GROUP_START) {
                $msg = $msg->label($Label);
            }
            switch($Type) {
                case self::DUMP:
                case self::LOG:
                    return $msg->log($Object);
                case self::INFO:
                    return $msg->info($Object);
                case self::WARN:
                    return $msg->warn($Object);
                case self::ERROR:
                    return $msg->error($Object);
                case self::TRACE:
                    return $msg->trace($Object);
                case self::EXCEPTION:
                	return $this->plugin('engine')->handleException($Object, $msg);
                case self::TABLE:
                    if (isset($Object[0]) && !is_string($Object[0]) && $Label) {
                        $Object = array($Label, $Object);
                    }
                    return $msg->table($Object[0], array_slice($Object[1],1), $Object[1][0]);
                case self::GROUP_START:
                	$insightGroupStack[] = $msg->group(md5($Label))->open();
                    return $msg->log($Label);
                case self::GROUP_END:
                	if(count($insightGroupStack)==0) {
                	    throw new Error('Too many groupEnd() as opposed to group() calls!');
                	}
                	$group = array_pop($insightGroupStack);
                    return $group->close();
	            default:
                    return $msg->log($Object);
            }
        }

        if (!$this->detectClientExtension()) {
            return false;
        }
      
        $meta = array();
        $skipFinalObjectEncode = false;
      
        if ($Object instanceof Exception) {
    
            $meta['file'] = $this->_escapeTraceFile($Object->getFile());
            $meta['line'] = $Object->getLine();
          
            $trace = $Object->getTrace();
            if ($Object instanceof ErrorException
               && isset($trace[0]['function'])
               && $trace[0]['function']=='errorHandler'
               && isset($trace[0]['class'])
               && $trace[0]['class']=='FirePHP') {
               
                $severity = false;
                switch($Object->getSeverity()) {
                    case E_WARNING: $severity = 'E_WARNING'; break;
                    case E_NOTICE: $severity = 'E_NOTICE'; break;
                    case E_USER_ERROR: $severity = 'E_USER_ERROR'; break;
                    case E_USER_WARNING: $severity = 'E_USER_WARNING'; break;
                    case E_USER_NOTICE: $severity = 'E_USER_NOTICE'; break;
                    case E_STRICT: $severity = 'E_STRICT'; break;
                    case E_RECOVERABLE_ERROR: $severity = 'E_RECOVERABLE_ERROR'; break;
                    case E_DEPRECATED: $severity = 'E_DEPRECATED'; break;
                    case E_USER_DEPRECATED: $severity = 'E_USER_DEPRECATED'; break;
                }
                   
                $Object = array('Class'=>get_class($Object),
                                'Message'=>$severity.': '.$Object->getMessage(),
                                'File'=>$this->_escapeTraceFile($Object->getFile()),
                                'Line'=>$Object->getLine(),
                                'Type'=>'trigger',
                                'Trace'=>$this->_escapeTrace(array_splice($trace,2)));
                $skipFinalObjectEncode = true;
            } else {
                $Object = array('Class'=>get_class($Object),
                                'Message'=>$Object->getMessage(),
                                'File'=>$this->_escapeTraceFile($Object->getFile()),
                                'Line'=>$Object->getLine(),
                                'Type'=>'throw',
                                'Trace'=>$this->_escapeTrace($trace));
                $skipFinalObjectEncode = true;
            }
            $Type = self::EXCEPTION;
          
        } else if ($Type==self::TRACE) {
          
            $trace = debug_backtrace();
            if (!$trace) return false;
            for( $i=0 ; $i<sizeof($trace) ; $i++ ) {
    
                if (isset($trace[$i]['class'])
                   && isset($trace[$i]['file'])
                   && ($trace[$i]['class']=='FirePHP'
                       || $trace[$i]['class']=='FB')
                   && (substr($this->_standardizePath($trace[$i]['file']),-18,18)=='FirePHPCore/fb.php'
                       || substr($this->_standardizePath($trace[$i]['file']),-29,29)=='FirePHPCore/FirePHP.class.php')) {
                    
                } else
                if (isset($trace[$i]['class'])
                   && isset($trace[$i+1]['file'])
                   && $trace[$i]['class']=='FirePHP'
                   && substr($this->_standardizePath($trace[$i+1]['file']),-18,18)=='FirePHPCore/fb.php') {
                    
                } else
                if ($trace[$i]['function']=='fb'
                   || $trace[$i]['function']=='trace'
                   || $trace[$i]['function']=='send') {

                    $Object = array('Class'=>isset($trace[$i]['class'])?$trace[$i]['class']:'',
                                    'Type'=>isset($trace[$i]['type'])?$trace[$i]['type']:'',
                                    'Function'=>isset($trace[$i]['function'])?$trace[$i]['function']:'',
                                    'Message'=>$trace[$i]['args'][0],
                                    'File'=>isset($trace[$i]['file'])?$this->_escapeTraceFile($trace[$i]['file']):'',
                                    'Line'=>isset($trace[$i]['line'])?$trace[$i]['line']:'',
                                    'Args'=>isset($trace[$i]['args'])?$this->encodeObject($trace[$i]['args']):'',
                                    'Trace'=>$this->_escapeTrace(array_splice($trace,$i+1)));
        
                    $skipFinalObjectEncode = true;
                    $meta['file'] = isset($trace[$i]['file'])?$this->_escapeTraceFile($trace[$i]['file']):'';
                    $meta['line'] = isset($trace[$i]['line'])?$trace[$i]['line']:'';
                    break;
                }
            }
    
        } else if ($Type==self::TABLE) {
          
            if (isset($Object[0]) && is_string($Object[0])) {
                $Object[1] = $this->encodeTable($Object[1]);
            } else {
                $Object = $this->encodeTable($Object);
            }
    
            $skipFinalObjectEncode = true;
          
        } else if ($Type==self::GROUP_START) {
          
            if (!$Label) {
                throw $this->newException('You must specify a label for the group!');
            }
          
        } else {
            if ($Type===null) {
                $Type = self::LOG;
            }
        }
        
        if ($this->options['includeLineNumbers']) {
            if (!isset($meta['file']) || !isset($meta['line'])) {
    
                $trace = debug_backtrace();
                for( $i=0 ; $trace && $i<sizeof($trace) ; $i++ ) {
          
                    if (isset($trace[$i]['class'])
                       && isset($trace[$i]['file'])
                       && ($trace[$i]['class']=='FirePHP'
                           || $trace[$i]['class']=='FB')
                       && (substr($this->_standardizePath($trace[$i]['file']),-18,18)=='FirePHPCore/fb.php'
                           || substr($this->_standardizePath($trace[$i]['file']),-29,29)=='FirePHPCore/FirePHP.class.php')) {
                        
                    } else
                    if (isset($trace[$i]['class'])
                       && isset($trace[$i+1]['file'])
                       && $trace[$i]['class']=='FirePHP'
                       && substr($this->_standardizePath($trace[$i+1]['file']),-18,18)=='FirePHPCore/fb.php') {
                        
                    } else
                    if (isset($trace[$i]['file'])
                       && substr($this->_standardizePath($trace[$i]['file']),-18,18)=='FirePHPCore/fb.php') {
                        
                    } else {
                        $meta['file'] = isset($trace[$i]['file'])?$this->_escapeTraceFile($trace[$i]['file']):'';
                        $meta['line'] = isset($trace[$i]['line'])?$trace[$i]['line']:'';
                        break;
                    }
                }      
            }
        } else {
            unset($meta['file']);
            unset($meta['line']);
        }

        $this->setHeader('X-Wf-Protocol-1','http://meta.wildfirehq.org/Protocol/JsonStream/0.2');
        $this->setHeader('X-Wf-1-Plugin-1','http://meta.firephp.org/Wildfire/Plugin/FirePHP/Library-FirePHPCore/'.self::VERSION);
     
        $structure_index = 1;
        if ($Type==self::DUMP) {
            $structure_index = 2;
            $this->setHeader('X-Wf-1-Structure-2','http://meta.firephp.org/Wildfire/Structure/FirePHP/Dump/0.1');
        } else {
            $this->setHeader('X-Wf-1-Structure-1','http://meta.firephp.org/Wildfire/Structure/FirePHP/FirebugConsole/0.1');
        }
      
        if ($Type==self::DUMP) {
            $msg = '{"'.$Label.'":'.$this->jsonEncode($Object, $skipFinalObjectEncode).'}';
        } else {
            $msg_meta = $Options;
            $msg_meta['Type'] = $Type;
            if ($Label!==null) {
                $msg_meta['Label'] = $Label;
            }
            if (isset($meta['file']) && !isset($msg_meta['File'])) {
                $msg_meta['File'] = $meta['file'];
            }
            if (isset($meta['line']) && !isset($msg_meta['Line'])) {
                $msg_meta['Line'] = $meta['line'];
            }
            $msg = '['.$this->jsonEncode($msg_meta).','.$this->jsonEncode($Object, $skipFinalObjectEncode).']';
        }
        
        $parts = explode("\n",chunk_split($msg, 5000, "\n"));
    
        for( $i=0 ; $i<count($parts) ; $i++) {
            
            $part = $parts[$i];
            if ($part) {
                
                if (count($parts)>2) {
                    
                    $this->setHeader('X-Wf-1-'.$structure_index.'-'.'1-'.$this->messageIndex,
                                     (($i==0)?strlen($msg):'')
                                     . '|' . $part . '|'
                                     . (($i<count($parts)-2)?'\\':''));
                } else {
                    $this->setHeader('X-Wf-1-'.$structure_index.'-'.'1-'.$this->messageIndex,
                                     strlen($part) . '|' . $part . '|');
                }
                
                $this->messageIndex++;
                
                if ($this->messageIndex > 99999) {
                    throw $this->newException('Maximum number (99,999) of messages reached!');             
                }
            }
        }
    
        $this->setHeader('X-Wf-1-Index',$this->messageIndex-1);    
        return true;
    }
  
    protected function _standardizePath($Path)
    {
        return preg_replace('/\\\\+/','/',$Path);    
    }
  
    protected function _escapeTrace($Trace)
    {
        if (!$Trace) return $Trace;
        for( $i=0 ; $i<sizeof($Trace) ; $i++ ) {
            if (isset($Trace[$i]['file'])) {
                $Trace[$i]['file'] = $this->_escapeTraceFile($Trace[$i]['file']);
            }
            if (isset($Trace[$i]['args'])) {
                $Trace[$i]['args'] = $this->encodeObject($Trace[$i]['args']);
            }
        }
        return $Trace;    
    }
  
    protected function _escapeTraceFile($File)
    {
        if (strpos($File,'\\')) {          
            return preg_replace('/\\\\+/','\\',$File);
        }
        return $File;
    }

    protected function headersSent(&$Filename, &$Linenum)
    {
        return headers_sent($Filename, $Linenum);
    }

    protected function setHeader($Name, $Value)
    {
        return header($Name.': '.$Value);
    }

    protected function getUserAgent()
    {
        if (!isset($_SERVER['HTTP_USER_AGENT'])) return false;
        return $_SERVER['HTTP_USER_AGENT'];
    }

    static function getAllRequestHeaders() {
        static $_cached_headers = false;
        if($_cached_headers!==false) {
            return $_cached_headers;
        }
        $headers = array();
        if(function_exists('getallheaders')) {
            foreach( getallheaders() as $name => $value ) {
                $headers[strtolower($name)] = $value;
            }
        } else {
            foreach($_SERVER as $name => $value) {
                if(substr($name, 0, 5) == 'HTTP_') {
                    $headers[strtolower(str_replace(' ', '-', str_replace('_', ' ', substr($name, 5))))] = $value;
                }
            }
        }
        return $_cached_headers = $headers;
    }

    protected function getRequestHeader($Name)
    {
        $headers = self::getAllRequestHeaders();
        if (isset($headers[strtolower($Name)])) {
            return $headers[strtolower($Name)];
        }
        return false;
    }

    protected function newException($Message)
    {
        return new Exception($Message);
    }
  
    function jsonEncode($Object, $skipObjectEncode = false)
    {
        if (!$skipObjectEncode) {
            $Object = $this->encodeObject($Object);
        }
        
        if (function_exists('json_encode')
           && $this->options['useNativeJsonEncode']!=false) {
    
            return json_encode($Object);
        } else {
            return $this->json_encode($Object);
        }
    }

    protected function encodeTable($Table)
    {
    
        if (!$Table) return $Table;
        
        $new_table = array();
        foreach($Table as $row) {
      
            if (is_array($row)) {
                $new_row = array();
            
                foreach($row as $item) {
                    $new_row[] = $this->encodeObject($item);
                }
            
                $new_table[] = $new_row;
            }
        }
        
        return $new_table;
    }

    protected function encodeObject($Object, $ObjectDepth = 1, $ArrayDepth = 1, $MaxDepth = 1)
    {
        if ($MaxDepth > $this->options['maxDepth']) {
            return '** Max Depth ('.$this->options['maxDepth'].') **';
        }

        $return = array();
    
        if (is_resource($Object)) {
    
            return '** '.(string)$Object.' **';
    
        } else    
        if (is_object($Object)) {
    
            if ($ObjectDepth > $this->options['maxObjectDepth']) {
                return '** Max Object Depth ('.$this->options['maxObjectDepth'].') **';
            }
            
            foreach ($this->objectStack as $refVal) {
                if ($refVal === $Object) {
                    return '** Recursion ('.get_class($Object).') **';
                }
            }
            array_push($this->objectStack, $Object);
                    
            $return['__className'] = $class = get_class($Object);
            $class_lower = strtolower($class);
    
            $reflectionClass = new ReflectionClass($class);  
            $properties = array();
            foreach( $reflectionClass->getProperties() as $property) {
                $properties[$property->getName()] = $property;
            }
                
            $members = (array)$Object;
    
            foreach( $properties as $plain_name => $property ) {
    
                $name = $raw_name = $plain_name;
                if ($property->isStatic()) {
                    $name = 'static:'.$name;
                }
                if ($property->isPublic()) {
                    $name = 'public:'.$name;
                } else
                if ($property->isPrivate()) {
                    $name = 'private:'.$name;
                    $raw_name = "\0".$class."\0".$raw_name;
                } else
                if ($property->isProtected()) {
                    $name = 'protected:'.$name;
                    $raw_name = "\0".'*'."\0".$raw_name;
                }
    
                if (!(isset($this->objectFilters[$class_lower])
                     && is_array($this->objectFilters[$class_lower])
                     && in_array($plain_name,$this->objectFilters[$class_lower]))) {
    
                    if (array_key_exists($raw_name,$members)
                       && !$property->isStatic()) {
                  
                        $return[$name] = $this->encodeObject($members[$raw_name], $ObjectDepth + 1, 1, $MaxDepth + 1);      
                
                    } else {
                        if (method_exists($property,'setAccessible')) {
                            $property->setAccessible(true);
                            $return[$name] = $this->encodeObject($property->getValue($Object), $ObjectDepth + 1, 1, $MaxDepth + 1);
                        } else
                        if ($property->isPublic()) {
                            $return[$name] = $this->encodeObject($property->getValue($Object), $ObjectDepth + 1, 1, $MaxDepth + 1);
                        } else {
                            $return[$name] = '** Need PHP 5.3 to get value **';
                        }
                    }
                } else {
                    $return[$name] = '** Excluded by Filter **';
                }
            }
            
            foreach( $members as $raw_name => $value ) {
    
                $name = $raw_name;
              
                if ($name{0} == "\0") {
                    $parts = explode("\0", $name);
                    $name = $parts[2];
                }
              
                $plain_name = $name;
    
                if (!isset($properties[$name])) {
                    $name = 'undeclared:'.$name;
    
                    if (!(isset($this->objectFilters[$class_lower])
                         && is_array($this->objectFilters[$class_lower])
                         && in_array($plain_name,$this->objectFilters[$class_lower]))) {
    
                        $return[$name] = $this->encodeObject($value, $ObjectDepth + 1, 1, $MaxDepth + 1);
                    } else {
                        $return[$name] = '** Excluded by Filter **';
                    }
                }
            }
            
            array_pop($this->objectStack);
            
        } elseif (is_array($Object)) {
    
            if ($ArrayDepth > $this->options['maxArrayDepth']) {
                return '** Max Array Depth ('.$this->options['maxArrayDepth'].') **';
            }
          
            foreach ($Object as $key => $val) {
              
                if ($key=='GLOBALS'
                   && is_array($val)
                   && array_key_exists('GLOBALS',$val)) {
                    $val['GLOBALS'] = '** Recursion (GLOBALS) **';
                }
              
                $return[$key] = $this->encodeObject($val, 1, $ArrayDepth + 1, $MaxDepth + 1);
            }
        } else {
            if (self::is_utf8($Object)) {
                return $Object;
            } else {
                return utf8_encode($Object);
            }
        }
        return $return;
    }

    protected static function is_utf8($str)
    {
        if(function_exists('mb_detect_encoding')) {
            return (mb_detect_encoding($str) == 'UTF-8');
        }
        $c=0; $b=0;
        $bits=0;
        $len=strlen($str);
        for($i=0; $i<$len; $i++){
            $c=ord($str[$i]);
            if ($c > 128){
                if (($c >= 254)) return false;
                elseif ($c >= 252) $bits=6;
                elseif ($c >= 248) $bits=5;
                elseif ($c >= 240) $bits=4;
                elseif ($c >= 224) $bits=3;
                elseif ($c >= 192) $bits=2;
                else return false;
                if (($i+$bits) > $len) return false;
                while($bits > 1){
                    $i++;
                    $b=ord($str[$i]);
                    if ($b < 128 || $b > 191) return false;
                    $bits--;
                }
            }
        }
        return true;
    }      
    
    private $json_objectStack = array();

    private function json_utf82utf16($utf8)
    {
        if (function_exists('mb_convert_encoding')) {
            return mb_convert_encoding($utf8, 'UTF-16', 'UTF-8');
        }

        switch(strlen($utf8)) {
            case 1:                
                return $utf8;

            case 2:
                return chr(0x07 & (ord($utf8{0}) >> 2))
                       . chr((0xC0 & (ord($utf8{0}) << 6))
                       | (0x3F & ord($utf8{1})));

            case 3:
                return chr((0xF0 & (ord($utf8{0}) << 4))
                       | (0x0F & (ord($utf8{1}) >> 2)))
                       . chr((0xC0 & (ord($utf8{1}) << 6))
                       | (0x7F & ord($utf8{2})));
        }
        return '';
    }
  
    private function json_encode($var)
    {
    
        if (is_object($var)) {
            if (in_array($var,$this->json_objectStack)) {
                return '"** Recursion **"';
            }
        }
          
        switch (gettype($var)) {
            case 'boolean':
                return $var ? 'true' : 'false';

            case 'NULL':
                return 'null';

            case 'integer':
                return (int) $var;

            case 'double':
            case 'float':
                return (float) $var;

            case 'string':
                $ascii = '';
                $strlen_var = strlen($var);
                for ($c = 0; $c < $strlen_var; ++$c) {

                    $ord_var_c = ord($var{$c});

                    switch (true) {
                        case $ord_var_c == 0x08:
                            $ascii .= '\b';
                            break;
                        case $ord_var_c == 0x09:
                            $ascii .= '\t';
                            break;
                        case $ord_var_c == 0x0A:
                            $ascii .= '\n';
                            break;
                        case $ord_var_c == 0x0C:
                            $ascii .= '\f';
                            break;
                        case $ord_var_c == 0x0D:
                            $ascii .= '\r';
                            break;

                        case $ord_var_c == 0x22:
                        case $ord_var_c == 0x2F:
                        case $ord_var_c == 0x5C:
                            $ascii .= '\\'.$var{$c};
                            break;

                        case (($ord_var_c >= 0x20) && ($ord_var_c <= 0x7F)):
                            $ascii .= $var{$c};
                            break;

                        case (($ord_var_c & 0xE0) == 0xC0):
                            $char = pack('C*', $ord_var_c, ord($var{$c + 1}));
                            $c += 1;
                            $utf16 = $this->json_utf82utf16($char);
                            $ascii .= sprintf('\u%04s', bin2hex($utf16));
                            break;

                        case (($ord_var_c & 0xF0) == 0xE0):
                            $char = pack('C*', $ord_var_c,
                                         ord($var{$c + 1}),
                                         ord($var{$c + 2}));
                            $c += 2;
                            $utf16 = $this->json_utf82utf16($char);
                            $ascii .= sprintf('\u%04s', bin2hex($utf16));
                            break;

                        case (($ord_var_c & 0xF8) == 0xF0):
                            $char = pack('C*', $ord_var_c,
                                         ord($var{$c + 1}),
                                         ord($var{$c + 2}),
                                         ord($var{$c + 3}));
                            $c += 3;
                            $utf16 = $this->json_utf82utf16($char);
                            $ascii .= sprintf('\u%04s', bin2hex($utf16));
                            break;

                        case (($ord_var_c & 0xFC) == 0xF8):
                            $char = pack('C*', $ord_var_c,
                                         ord($var{$c + 1}),
                                         ord($var{$c + 2}),
                                         ord($var{$c + 3}),
                                         ord($var{$c + 4}));
                            $c += 4;
                            $utf16 = $this->json_utf82utf16($char);
                            $ascii .= sprintf('\u%04s', bin2hex($utf16));
                            break;

                        case (($ord_var_c & 0xFE) == 0xFC):
                            $char = pack('C*', $ord_var_c,
                                         ord($var{$c + 1}),
                                         ord($var{$c + 2}),
                                         ord($var{$c + 3}),
                                         ord($var{$c + 4}),
                                         ord($var{$c + 5}));
                            $c += 5;
                            $utf16 = $this->json_utf82utf16($char);
                            $ascii .= sprintf('\u%04s', bin2hex($utf16));
                            break;
                    }
                }

                return '"'.$ascii.'"';

            case 'array':
                if (is_array($var) && count($var) && (array_keys($var) !== range(0, sizeof($var) - 1))) {
                  
                    $this->json_objectStack[] = $var;

                    $properties = array_map(array($this, 'json_name_value'),
                                            array_keys($var),
                                            array_values($var));

                    array_pop($this->json_objectStack);

                    foreach($properties as $property) {
                        if ($property instanceof Exception) {
                            return $property;
                        }
                    }

                    return '{' . join(',', $properties) . '}';
                }

                $this->json_objectStack[] = $var;

                $elements = array_map(array($this, 'json_encode'), $var);

                array_pop($this->json_objectStack);

                foreach($elements as $element) {
                    if ($element instanceof Exception) {
                        return $element;
                    }
                }

                return '[' . join(',', $elements) . ']';

            case 'object':
                $vars = self::encodeObject($var);

                $this->json_objectStack[] = $var;

                $properties = array_map(array($this, 'json_name_value'),
                                        array_keys($vars),
                                        array_values($vars));

                array_pop($this->json_objectStack);
              
                foreach($properties as $property) {
                    if ($property instanceof Exception) {
                        return $property;
                    }
                }
                     
                return '{' . join(',', $properties) . '}';

            default:
                return null;
        }
    }

    private function json_name_value($name, $value)
    {
        if ($name=='GLOBALS'
           && is_array($value)
           && array_key_exists('GLOBALS',$value)) {
            $value['GLOBALS'] = '** Recursion **';
        }
    
        $encoded_value = $this->json_encode($value);

        if ($encoded_value instanceof Exception) {
            return $encoded_value;
        }

        return $this->json_encode(strval($name)) . ':' . $encoded_value;
    }

    function setProcessorUrl($URL)
    {
        trigger_error("The FirePHP::setProcessorUrl() method is no longer supported", E_USER_DEPRECATED);
    }

    function setRendererUrl($URL)
    {
        trigger_error("The FirePHP::setRendererUrl() method is no longer supported", E_USER_DEPRECATED);
    }  
}
