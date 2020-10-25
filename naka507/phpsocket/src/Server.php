<?php
namespace Naka507\Socket;

class Server {
    const OS_TYPE_LINUX = 'linux';
    const OS_TYPE_WINDOWS = 'windows';

    public static $_OS = 'linux';
    public static $_outputStream = null;
    public static $_outputDecorated = false;
    public static $_gracefulStop = false;
    public static $_startFile = '';
    public static $_daemonize = false;
    public static $event = null;
    public static $pidFile = "";

	public $protocol;
	public $transport;
	public $onWorkerStart;
	public $onWorkerStop;
	public $onMessage;
	public $onClose;
	public $onError;
	public $onBufferDrain;
    public $onBufferFull;

    protected $_socket = null;
    protected $_pause = true;
    protected $_context = [];
    

	function __construct($port = 8000, $option = array()) {
        Server::init();
        Http::init();
		$this->port = $port;
		$this->protocol = 'Http';
        $this->_socket = null;
        $this->onMessage = $this->onClose = $this->onError = $this->onBufferDrain = $this->onBufferFull = null;

        // Only for cli.
        if (php_sapi_name() != "cli") {
            exit("only run in command line mode \n");
        }

        if ( DIRECTORY_SEPARATOR === '\\' ) {
            Server::$_OS = Server::OS_TYPE_WINDOWS;
        }

        $this->_context = stream_context_create($option);
	}

	public function start(){

        Server::command();

		$local_socket = "tcp://0.0.0.0:".$this->port;
        
        $flags = STREAM_SERVER_BIND | STREAM_SERVER_LISTEN;
		$errno = 0;
		$errmsg = '';
		$this->_socket = stream_socket_server($local_socket, $errno, $errmsg, $flags,$this->_context);
		if (!$this->_socket) {
			throw new Exception($errmsg);
        }

        if ($this->transport === 'ssl') {
            stream_socket_enable_crypto($this->_socket, false);
        }

		stream_set_blocking($this->_socket, 0);

		if (!Server::$event) {
            Server::$event = new Events();
		}

		Server::$event->add($this->_socket, Events::EV_READ, array($this, 'accept'));

		Timer::init(Server::$event);
		
		if (empty($this->onMessage)) {
            $this->onMessage = function () {};
		}

		// Try to emit onWorkerStart callback.
        if ($this->onWorkerStart) {
            try {
                call_user_func($this->onWorkerStart, $this);
            } catch (\Exception $e) {
                Server::log($e);
                // Avoid rapid infinite loop exit.
                sleep(1);
                exit(250);
            } catch (\Error $e) {
                Server::log($e);
                // Avoid rapid infinite loop exit.
                sleep(1);
                exit(250);
            }
        }

		Server::$event->loop();
	}

    public function accept($socket)
    {
        // Accept a connection on server socket.
        set_error_handler(function(){});
        $client = stream_socket_accept($socket, 0, $remote_address);
        restore_error_handler();

        // Thundering herd.
        if (!$client) {
            return;
        }

        $connection                         = new Connection($client, $remote_address);
        $this->connections[$connection->id] = $connection;
        $connection->worker                 = $this;
        $connection->protocol               = $this->protocol;
        $connection->transport              = $this->transport;
        $connection->onMessage              = $this->onMessage;
        $connection->onClose                = $this->onClose;
        $connection->onError                = $this->onError;
        $connection->onBufferDrain          = $this->onBufferDrain;
        $connection->onBufferFull           = $this->onBufferFull;

        // Try to emit onConnect callback.
        if ($this->onConnect) {
            try {
                call_user_func($this->onConnect, $connection);
            } catch (\Exception $e) {
                Server::log($e);
                exit(250);
            } catch (\Error $e) {
                Server::log($e);
                exit(250);
            }
        }
    }

    public function pause()
    {
        if (Server::$event && false === $this->_pause && $this->_socket) {
            Server::$event->del($this->_socket, Events::EV_READ);
            $this->_pause = true;
        }
    }

    public function stop()
    {
        // Try to emit onWorkerStop callback.
        if ($this->onWorkerStop) {
            try {
                call_user_func($this->onWorkerStop, $this);
            } catch (\Exception $e) {
                Server::log($e);
                exit(250);
            } catch (\Error $e) {
                Server::log($e);
                exit(250);
            }
        }
        // Remove listener for server socket.
        $this->pause();
        if ($this->_socket) {
            set_error_handler(function(){});
            fclose($this->_socket);
            restore_error_handler();
            $this->_socket = null;
        }
        // Close all connections for the worker.
        foreach ($this->connections as $connection) {
            $connection->close();
        }
        // Clear callback.
        $this->onMessage = $this->onClose = $this->onError = $this->onBufferDrain = $this->onBufferFull = null;
    }

    public static function init(){

        if( empty(Server::$pidFile ) ){
            Server::$pidFile = getcwd() . "/server.pid";
        }
        
    }

    public static function getGracefulStop(){
        return static::$_gracefulStop;
    }

    public static function command(){
        if (Server::$_OS !== Server::OS_TYPE_LINUX ) {
            return;
        }
        global $argv;
        $command  = isset($argv[1]) ? trim($argv[1]) : '';

        // Get master process PID.
        $master_pid      = is_file(Server::$pidFile) ? file_get_contents(Server::$pidFile) : 0;

        switch ($command) {
            case '-s':
                $master_pid && posix_kill($master_pid, SIGINT);
                exit(0);
                break;
            case '-d':
                Server::$_daemonize = true;
                Server::console("Start success.Input \"php $argv[0] -s\" to stop.\n");
                Server::daemonize();
                break;
            default :
                Server::console("Start success.Press Ctrl+C to stop.\n");
                break;
        }

        $master_pid = posix_getpid();

        if (false === file_put_contents(Server::$pidFile, $master_pid)) {
            throw new \Exception('can not save pid to ' . Server::$pidFile);
        }
    }

    public static function daemonize()
    {
		$pid = pcntl_fork();
		if ($pid == -1) {
			throw new \Exception('fork子进程失败');
		} elseif ($pid > 0) {
			exit(0);
		}
		
		$sid = posix_setsid();
		if ($sid == -1) {
			throw new \Exception('setsid fail');
		}
		
		chdir('/');
		
		$pid = pcntl_fork();
		if ($pid == -1) {
			throw new \Exception('fork子进程失败');
		} elseif ($pid > 0) {
			exit(0);
		}
		fclose(STDIN);
		fclose(STDOUT);
		fclose(STDERR);
    }

    public static function log($msg)
    {
        $msg = $msg . "\n";
        if (!Server::$_daemonize) {
            Server::console($msg);
        }
        file_put_contents((string)Server::$logFile, date('Y-m-d H:i:s') . ' ' . 'pid:'
            . (Server::$_OS === Server::OS_TYPE_LINUX ? posix_getpid() : 1) . ' ' . $msg, FILE_APPEND | LOCK_EX);
    }

    public static function console($msg, $decorated = false)
    {
        $stream = Server::outputStream();
        if (!$stream) {
            return false;
        }
        if (!$decorated) {
            $line = $white = $green = $end = '';
            if (Server::$_outputDecorated) {
                $line = "\033[1A\n\033[K";
                $white = "\033[47;30m";
                $green = "\033[32;40m";
                $end = "\033[0m";
            }
            $msg = str_replace(array('<n>', '<w>', '<g>'), array($line, $white, $green), $msg);
            $msg = str_replace(array('</n>', '</w>', '</g>'), $end, $msg);
        } elseif (!Server::$_outputDecorated) {
            return false;
        }
        fwrite($stream, $msg);
        fflush($stream);
        return true;
    }

    private static function outputStream($stream = null)
    {
        if (!$stream) {
            $stream = Server::$_outputStream ? Server::$_outputStream : STDOUT;
        }
        if (!$stream || !is_resource($stream) || 'stream' !== get_resource_type($stream)) {
            return false;
        }
        $stat = fstat($stream);
        if (($stat['mode'] & 0170000) === 0100000) {
            // file
            Server::$_outputDecorated = false;
        } else {
            Server::$_outputDecorated =
                Server::$_OS === Server::OS_TYPE_LINUX &&
                function_exists('posix_isatty') &&
                posix_isatty($stream);
        }
        return Server::$_outputStream = $stream;
    }

}