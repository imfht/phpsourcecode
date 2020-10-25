<?php
namespace Naka507\Socket;
use Exception;

class Connection
{
    const READ_BUFFER_SIZE = 65535;
    const STATUS_INITIAL = 0;
    const STATUS_CONNECTING = 1;
    const STATUS_ESTABLISHED = 2;
    const STATUS_CLOSING = 4;
    const STATUS_CLOSED = 8;
    public $onMessage = null;
    public $onClose = null;
    public $onError = null;
    public $onBufferFull = null;
    public $onBufferDrain = null;
    public $protocol = null;
    public $transport = 'tcp';
    public $worker = null;
    public $bytesRead = 0;
    public $bytesWritten = 0;
    public $id = 0;
    protected $_id = 0;
    public $maxSendBufferSize = 1048576;
    public static $defaultMaxSendBufferSize = 1048576;
    public $maxPackageSize = 1048576;
    public static $defaultMaxPackageSize = 10485760;
    protected static $_idRecorder = 1;
    protected $_socket = null;
    protected $_sendBuffer = '';
    protected $_recvBuffer = '';
    protected $_currentPackageLength = 0;
    protected $_status = self::STATUS_ESTABLISHED;
    protected $_remoteAddress = '';
    protected $_isPaused = false;
    protected $_sslHandshakeCompleted = false;
    public static $connections = array();

    public static $_statusToString = array(
        self::STATUS_INITIAL     => 'INITIAL',
        self::STATUS_CONNECTING  => 'CONNECTING',
        self::STATUS_ESTABLISHED => 'ESTABLISHED',
        self::STATUS_CLOSING     => 'CLOSING',
        self::STATUS_CLOSED      => 'CLOSED',
    );

    public static $statistics = array(
        'connection_count' => 0,
        'total_request'    => 0,
        'throw_exception'  => 0,
        'send_fail'        => 0,
    );

    public function __call($name, $arguments) {
        // Try to emit custom function within protocol
        if (method_exists("\Naka507\Socket\Http", $name)) {
            try {
                return call_user_func(array("\Naka507\Socket\Http", $name), $this, $arguments);
            } catch (\Exception $e) {
                Server::log($e);
                exit(250);
            } catch (\Error $e) {
                Server::log($e);
                exit(250);
            }
        }
    }

    public function __construct($socket, $remote_address = '')
    {
        self::$statistics['connection_count']++;
        $this->id = $this->_id = self::$_idRecorder++;
        if(self::$_idRecorder === PHP_INT_MAX){
            self::$_idRecorder = 0;
        }
        $this->_socket = $socket;
        stream_set_blocking($this->_socket, 0);
        // Compatible with hhvm
        if (function_exists('stream_set_read_buffer')) {
            stream_set_read_buffer($this->_socket, 0);
        }
        Server::$event->add($this->_socket, Events::EV_READ, array($this, 'read'));
        $this->maxSendBufferSize        = self::$defaultMaxSendBufferSize;
        $this->maxPackageSize           = self::$defaultMaxPackageSize;
        $this->_remoteAddress           = $remote_address;
        static::$connections[$this->id] = $this;
    }

    public function getStatus($raw_output = true)
    {
        if ($raw_output) {
            return $this->_status;
        }
        return self::$_statusToString[$this->_status];
    }

    public function send($send_buffer, $raw = false)
    {
        if ($this->_status === self::STATUS_CLOSING || $this->_status === self::STATUS_CLOSED) {
            return false;
        }

        // Try to call protocol::encode($send_buffer) before sending.
        if (false === $raw && $this->protocol !== null) {
            $parser      = $this->protocol;
            
            if ($send_buffer === '') {
                return null;
            }
        }

        if ($this->_status !== self::STATUS_ESTABLISHED ||
            ($this->transport === 'ssl' && $this->_sslHandshakeCompleted !== true)
        ) {
            if ($this->_sendBuffer) {
                if ($this->bufferIsFull()) {
                    self::$statistics['send_fail']++;
                    return false;
                }
            }
            $this->_sendBuffer .= $send_buffer;
            $this->checkBufferWillFull();
            return null;
        }

        // Attempt to send data directly.
        if ($this->_sendBuffer === '') {
            if ($this->transport === 'ssl') {
                Server::$event->add($this->_socket, Events::EV_WRITE, array($this, 'write'));
                $this->_sendBuffer = $send_buffer;
                $this->checkBufferWillFull();
                return null;
            }
            set_error_handler(function(){});
            $len = fwrite($this->_socket, $send_buffer);
            restore_error_handler();
            // send successful.
            if ($len === strlen($send_buffer)) {
                $this->bytesWritten += $len;
                return true;
            }
            // Send only part of the data.
            if ($len > 0) {
                $this->_sendBuffer = substr($send_buffer, $len);
                $this->bytesWritten += $len;
            } else {
                // Connection closed?
                if (!is_resource($this->_socket) || feof($this->_socket)) {
                    self::$statistics['send_fail']++;
                    if ($this->onError) {
                        try {
                            call_user_func($this->onError, $this, WORKERMAN_SEND_FAIL, 'client closed');
                        } catch (\Exception $e) {
                            Server::log($e);
                            exit(250);
                        } catch (\Error $e) {
                            Server::log($e);
                            exit(250);
                        }
                    }
                    $this->destroy();
                    return false;
                }
                $this->_sendBuffer = $send_buffer;
            }
            Server::$event->add($this->_socket, Events::EV_WRITE, array($this, 'write'));
            // Check if the send buffer will be full.
            $this->checkBufferWillFull();
            return null;
        } else {
            if ($this->bufferIsFull()) {
                self::$statistics['send_fail']++;
                return false;
            }

            $this->_sendBuffer .= $send_buffer;
            // Check if the send buffer is full.
            $this->checkBufferWillFull();
        }
    }

    public function getRemoteIp()
    {
        $pos = strrpos($this->_remoteAddress, ':');
        if ($pos) {
            return substr($this->_remoteAddress, 0, $pos);
        }
        return '';
    }

    public function getRemotePort()
    {
        if ($this->_remoteAddress) {
            return (int)substr(strrchr($this->_remoteAddress, ':'), 1);
        }
        return 0;
    }

    public function getRemoteAddress()
    {
        return $this->_remoteAddress;
    }

    public function getLocalIp()
    {
        $address = $this->getLocalAddress();
        $pos = strrpos($address, ':');
        if (!$pos) {
            return '';
        }
        return substr($address, 0, $pos);
    }

    public function getLocalPort()
    {
        $address = $this->getLocalAddress();
        $pos = strrpos($address, ':');
        if (!$pos) {
            return 0;
        }
        return (int)substr(strrchr($address, ':'), 1);
    }

    public function getLocalAddress()
    {
        return (string)@stream_socket_get_name($this->_socket, false);
    }

    public function getSendBufferQueueSize()
    {
        return strlen($this->_sendBuffer);
    }

    public function getRecvBufferQueueSize()
    {
        return strlen($this->_recvBuffer);
    }

    public function isIpV4()
    {
        if ($this->transport === 'unix') {
            return false;
        }
        return strpos($this->getRemoteIp(), ':') === false;
    }

    public function isIpV6()
    {
        if ($this->transport === 'unix') {
            return false;
        }
        return strpos($this->getRemoteIp(), ':') !== false;
    }

    public function pause()
    {
        Server::$event->del($this->_socket, Events::EV_READ);
        $this->_isPaused = true;
    }

    public function resume()
    {
        if ($this->_isPaused === true) {
            Server::$event->add($this->_socket, Events::EV_READ, array($this, 'read'));
            $this->_isPaused = false;
            $this->read($this->_socket, false);
        }
    }

    public function read($socket, $check_eof = true)
    {
        // SSL handshake.
        if ($this->transport === 'ssl' && $this->_sslHandshakeCompleted !== true) {
            if ($this->doSslHandshake($socket)) {
                $this->_sslHandshakeCompleted = true;
                if ($this->_sendBuffer) {
                    Server::$event->add($socket, Events::EV_WRITE, array($this, 'write'));
                }
            } else {
                return;
            }
        }

        set_error_handler(function(){});
        $buffer = fread($socket, self::READ_BUFFER_SIZE);
        restore_error_handler();

        // Check connection closed.
        if ($buffer === '' || $buffer === false) {
            if ($check_eof && (feof($socket) || !is_resource($socket) || $buffer === false)) {
                $this->destroy();
                return;
            }
        } else {
            $this->bytesRead += strlen($buffer);
            $this->_recvBuffer .= $buffer;
        }

        // If the application layer protocol has been set up.
        if ($this->protocol !== null) {
            $parser = $this->protocol;
            while ($this->_recvBuffer !== '' && !$this->_isPaused) {
                // The current packet length is known.
                if ($this->_currentPackageLength) {
                    // Data is not enough for a package.
                    if ($this->_currentPackageLength > strlen($this->_recvBuffer)) {
                        break;
                    }
                } else {
                    // Get current package length.
                    set_error_handler(function($code, $msg, $file, $line){
                        Server::console("$msg in file $file on line $line\n");
                    });
                    // $this->_currentPackageLength = $parser::input($this->_recvBuffer, $this);
                    $this->_currentPackageLength = Http::input($this->_recvBuffer, $this);
                    restore_error_handler();
                    // The packet length is unknown.
                    if ($this->_currentPackageLength === 0) {
                        break;
                    } elseif ($this->_currentPackageLength > 0 && $this->_currentPackageLength <= $this->maxPackageSize) {
                        // Data is not enough for a package.
                        if ($this->_currentPackageLength > strlen($this->_recvBuffer)) {
                            break;
                        }
                    } // Wrong package.
                    else {
                        Server::console('error package. package_length=' . var_export($this->_currentPackageLength, true));
                        $this->destroy();
                        return;
                    }
                }

                // The data is enough for a packet.
                self::$statistics['total_request']++;
                // The current packet length is equal to the length of the buffer.
                if (strlen($this->_recvBuffer) === $this->_currentPackageLength) {
                    $one_request_buffer = $this->_recvBuffer;
                    $this->_recvBuffer  = '';
                } else {
                    // Get a full package from the buffer.
                    $one_request_buffer = substr($this->_recvBuffer, 0, $this->_currentPackageLength);
                    // Remove the current package from the receive buffer.
                    $this->_recvBuffer = substr($this->_recvBuffer, $this->_currentPackageLength);
                }
                // Reset the current packet length to 0.
                $this->_currentPackageLength = 0;
                if (!$this->onMessage) {
                    continue;
                }
                try {
                    // Decode request buffer before Emitting onMessage callback.
                    // call_user_func($this->onMessage, $this, $parser::decode($one_request_buffer, $this));
                    $response = new Response($this);
                    $request = new Request($this,$one_request_buffer);
                    call_user_func($this->onMessage, $request, $response);
                } catch (\Exception $e) {
                    Server::log($e);
                    exit(250);
                } catch (\Error $e) {
                    Server::log($e);
                    exit(250);
                }
            }
            return;
        }

        if ($this->_recvBuffer === '' || $this->_isPaused) {
            return;
        }

        // Applications protocol is not set.
        self::$statistics['total_request']++;
        if (!$this->onMessage) {
            $this->_recvBuffer = '';
            return;
        }
        try {
            call_user_func($this->onMessage, $this, $this->_recvBuffer);
        } catch (\Exception $e) {
            Server::log($e);
            exit(250);
        } catch (\Error $e) {
            Server::log($e);
            exit(250);
        }
        // Clean receive buffer.
        $this->_recvBuffer = '';
    }

    public function write()
    {
        set_error_handler(function(){});
        if ($this->transport === 'ssl') {
            $len = fwrite($this->_socket, $this->_sendBuffer, 8192);
        } else {
            $len = fwrite($this->_socket, $this->_sendBuffer);
        }
        restore_error_handler();
        if ($len === strlen($this->_sendBuffer)) {
            $this->bytesWritten += $len;
            Server::$event->del($this->_socket, Events::EV_WRITE);
            $this->_sendBuffer = '';
            // Try to emit onBufferDrain callback when the send buffer becomes empty.
            if ($this->onBufferDrain) {
                try {
                    call_user_func($this->onBufferDrain, $this);
                } catch (\Exception $e) {
                    Server::log($e);
                    exit(250);
                } catch (\Error $e) {
                    Server::log($e);
                    exit(250);
                }
            }
            if ($this->_status === self::STATUS_CLOSING) {
                $this->destroy();
            }
            return true;
        }
        if ($len > 0) {
            $this->bytesWritten += $len;
            $this->_sendBuffer = substr($this->_sendBuffer, $len);
        } else {
            self::$statistics['send_fail']++;
            $this->destroy();
        }
    }

    public function doSslHandshake($socket){
        if (feof($socket)) {
            $this->destroy();
            return false;
        }
        $async = $this instanceof AsyncTcpConnection;
        if($async){
            $type = STREAM_CRYPTO_METHOD_SSLv2_CLIENT | STREAM_CRYPTO_METHOD_SSLv23_CLIENT;
        }else{
            $type = STREAM_CRYPTO_METHOD_SSLv2_SERVER | STREAM_CRYPTO_METHOD_SSLv23_SERVER;
        }

        // Hidden error.
        set_error_handler(function($errno, $errstr, $file){
            if (!Server::$_daemonize) {
                Server::console("SSL handshake error: $errstr \n");
            }
        });
        $ret     = stream_socket_enable_crypto($socket, true, $type);
        restore_error_handler();
        // Negotiation has failed.
        if (false === $ret) {
            $this->destroy();
            return false;
        } elseif (0 === $ret) {
            // There isn't enough data and should try again.
            return false;
        }
        if (isset($this->onSslHandshake)) {
            try {
                call_user_func($this->onSslHandshake, $this);
            } catch (\Exception $e) {
                Server::log($e);
                exit(250);
            } catch (\Error $e) {
                Server::log($e);
                exit(250);
            }
        }
        return true;
    }

    public function pipe($dest)
    {
        $source              = $this;
        $this->onMessage     = function ($source, $data) use ($dest) {
            $dest->send($data);
        };
        $this->onClose       = function ($source) use ($dest) {
            $dest->destroy();
        };
        $dest->onBufferFull  = function ($dest) use ($source) {
            $source->pause();
        };
        $dest->onBufferDrain = function ($dest) use ($source) {
            $source->resume();
        };
    }

    public function consumeRecvBuffer($length)
    {
        $this->_recvBuffer = substr($this->_recvBuffer, $length);
    }

    public function close($data = null, $raw = false)
    {
        if ($this->_status === self::STATUS_CLOSING || $this->_status === self::STATUS_CLOSED) {
            return;
        } else {
            if ($data !== null) {
                $this->send($data, $raw);
            }
            $this->_status = self::STATUS_CLOSING;
        }
        if ($this->_sendBuffer === '') {
            $this->destroy();
        } else {
            $this->pause();
        }
    }

    public function getSocket()
    {
        return $this->_socket;
    }

    protected function checkBufferWillFull()
    {
        if ($this->maxSendBufferSize <= strlen($this->_sendBuffer)) {
            if ($this->onBufferFull) {
                try {
                    call_user_func($this->onBufferFull, $this);
                } catch (\Exception $e) {
                    Server::log($e);
                    exit(250);
                } catch (\Error $e) {
                    Server::log($e);
                    exit(250);
                }
            }
        }
    }

    protected function bufferIsFull()
    {
        // Buffer has been marked as full but still has data to send then the packet is discarded.
        if ($this->maxSendBufferSize <= strlen($this->_sendBuffer)) {
            if ($this->onError) {
                try {
                    call_user_func($this->onError, $this, WORKERMAN_SEND_FAIL, 'send buffer full and drop package');
                } catch (\Exception $e) {
                    Server::log($e);
                    exit(250);
                } catch (\Error $e) {
                    Server::log($e);
                    exit(250);
                }
            }
            return true;
        }
        return false;
    }

    public function bufferIsEmpty()
    {
    	return empty($this->_sendBuffer);
    }

    public function destroy()
    {
        // Avoid repeated calls.
        if ($this->_status === self::STATUS_CLOSED) {
            return;
        }
        // Remove event listener.
        Server::$event->del($this->_socket, Events::EV_READ);
        Server::$event->del($this->_socket, Events::EV_WRITE);

        // Close socket.
        set_error_handler(function(){});
        fclose($this->_socket);
        restore_error_handler();

        $this->_status = self::STATUS_CLOSED;
        // Try to emit onClose callback.
        if ($this->onClose) {
            try {
                call_user_func($this->onClose, $this);
            } catch (\Exception $e) {
                Server::log($e);
                exit(250);
            } catch (\Error $e) {
                Server::log($e);
                exit(250);
            }
        }
        // Try to emit protocol::onClose
        if ($this->protocol && method_exists($this->protocol, 'onClose')) {
            try {
                call_user_func(array($this->protocol, 'onClose'), $this);
            } catch (\Exception $e) {
                Server::log($e);
                exit(250);
            } catch (\Error $e) {
                Server::log($e);
                exit(250);
            }
        }
        if ($this->_status === self::STATUS_CLOSED) {
            // Cleaning up the callback to avoid memory leaks.
            $this->onMessage = $this->onClose = $this->onError = $this->onBufferFull = $this->onBufferDrain = null;
            // Remove from worker->connections.
            if ($this->worker) {
                unset($this->worker->connections[$this->_id]);
            }
            unset(static::$connections[$this->_id]);
        }
    }

    /**
     * Destruct.
     *
     * @return void
     */
    public function __destruct()
    {
        static $mod;
        self::$statistics['connection_count']--;
        if (Server::getGracefulStop()) {
            if (!isset($mod)) {
                $mod = ceil((self::$statistics['connection_count'] + 1) / 3);
            }

            if (0 === self::$statistics['connection_count'] % $mod) {
                Server::log('worker[' . posix_getpid() . '] remains ' . self::$statistics['connection_count'] . ' connection(s)');
            }

            if(0 === self::$statistics['connection_count']) {
                Server::stop();
            }
        }
    }
}
