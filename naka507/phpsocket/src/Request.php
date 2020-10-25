<?php
namespace Naka507\Socket;
class Request
{
    public $connection;
    public $header;
    public $server;
    public $cookie;
    public $request;
    public $post;
    public $get;
    public $files;
    public $session;
    public $rawdata;

    public function __construct(Connection $connection, $buffer)
    {
        $this->connection = $connection;
        $this->header = [];
        $_POST = $_GET = $_COOKIE = $_REQUEST = $_SESSION = $_FILES = array();
        $GLOBALS['HTTP_RAW_POST_DATA'] = '';
        // Clear cache.
        Http::$header   = array('Connection' => 'Connection: keep-alive');
        Http::$instance = new Http();
        // $_SERVER
        $_SERVER = array(
            'SCRIPT_NAME'         => '',
            'QUERY_STRING'         => '',
            'REQUEST_METHOD'       => '',
            'REQUEST_URI'          => '',
            'SERVER_PROTOCOL'      => '',
            'SERVER_SOFTWARE'      => 'web server',
            'SERVER_NAME'          => '',
            'HTTP_HOST'            => '',
            'HTTP_USER_AGENT'      => '',
            'HTTP_ACCEPT'          => '',
            'HTTP_ACCEPT_LANGUAGE' => '',
            'HTTP_ACCEPT_ENCODING' => '',
            'HTTP_COOKIE'          => '',
            'HTTP_CONNECTION'      => '',
            'CONTENT_TYPE'         => '',
            'REMOTE_ADDR'          => '',
            'REMOTE_PORT'          => '0',
            'REQUEST_TIME'         => time()
        );

        // Parse headers.
        list($http_header, $http_body) = explode("\r\n\r\n", $buffer, 2);
        $header_data = explode("\r\n", $http_header);

        list($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI'], $_SERVER['SERVER_PROTOCOL']) = explode(' ',
            $header_data[0]);

        $http_post_boundary = '';
        unset($header_data[0]);

        $this->header['REQUEST_METHOD'] = $_SERVER['REQUEST_METHOD'];
        $this->header['REQUEST_URI'] = $_SERVER['REQUEST_URI'];
        $this->header['SERVER_PROTOCOL'] = $_SERVER['SERVER_PROTOCOL'];
        
        foreach ($header_data as $content) {
            // \r\n\r\n
            if (empty($content)) {
                continue;
            }
            list($key, $value)       = explode(':', $content, 2);
            $key                     = str_replace('-', '_', strtoupper($key));
            $value                   = trim($value);
            $_SERVER['HTTP_' . $key] = $value;
            $this->header[$key] = $value;
            switch ($key) {
                // HTTP_HOST
                case 'HOST':
                    $tmp                    = explode(':', $value);
                    $_SERVER['SERVER_NAME'] = $tmp[0];
                    if (isset($tmp[1])) {
                        $_SERVER['SERVER_PORT'] = $tmp[1];
                    }
                    break;
                // cookie
                case 'COOKIE':
                    parse_str(str_replace('; ', '&', $_SERVER['HTTP_COOKIE']), $_COOKIE);
                    break;
                // content-type
                case 'CONTENT_TYPE':
                    if (!preg_match('/boundary="?(\S+)"?/', $value, $match)) {
                        if ($pos = strpos($value, ';')) {
                            $_SERVER['CONTENT_TYPE'] = substr($value, 0, $pos);
                        } else {
                            $_SERVER['CONTENT_TYPE'] = $value;
                        }
                    } else {
                        $_SERVER['CONTENT_TYPE'] = 'multipart/form-data';
                        $http_post_boundary      = '--' . $match[1];
                    }
                    break;
                case 'CONTENT_LENGTH':
                    $_SERVER['CONTENT_LENGTH'] = $value;
                    break;
                case 'UPGRADE':
                    break;
            }
        }
		if(isset($_SERVER['HTTP_ACCEPT_ENCODING']) && strpos($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip') !== FALSE){
			Http::$gzip = true;
		}
        // Parse $_POST.
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (isset($_SERVER['CONTENT_TYPE'])) {
                switch ($_SERVER['CONTENT_TYPE']) {
                    case 'multipart/form-data':
                        self::parseUploadFiles($http_body, $http_post_boundary);
                        break;
                    case 'application/json':
                        $_POST = json_decode($http_body, true);
                        break;
                    case 'application/x-www-form-urlencoded':
                        parse_str($http_body, $_POST);
                        break;
                }
            }
        }

        // Parse other HTTP action parameters
        if ($_SERVER['REQUEST_METHOD'] != 'GET' && $_SERVER['REQUEST_METHOD'] != "POST") {
            $data = array();
            if ($_SERVER['CONTENT_TYPE'] === "application/x-www-form-urlencoded") {
                parse_str($http_body, $data);
            } elseif ($_SERVER['CONTENT_TYPE'] === "application/json") {
                $data = json_decode($http_body, true);
            }
            $_REQUEST = array_merge($_REQUEST, $data);
        }

        // HTTP_RAW_REQUEST_DATA HTTP_RAW_POST_DATA
        $GLOBALS['HTTP_RAW_REQUEST_DATA'] = $GLOBALS['HTTP_RAW_POST_DATA'] = $http_body;
        $this->rawdata = $GLOBALS['HTTP_RAW_REQUEST_DATA'];

        // QUERY_STRING
        $_SERVER['QUERY_STRING'] = parse_url($_SERVER['REQUEST_URI'], PHP_URL_QUERY);
        if ($_SERVER['QUERY_STRING']) {
            // $GET
            parse_str($_SERVER['QUERY_STRING'], $_GET);
        } else {
            $_SERVER['QUERY_STRING'] = '';
        }

        if (is_array($_POST)) {
            // REQUEST
            $_REQUEST = array_merge($_GET, $_POST, $_REQUEST);
        } else {
            // REQUEST
            $_REQUEST = array_merge($_GET, $_REQUEST);
        }

        // REMOTE_ADDR REMOTE_PORT
        $_SERVER['REMOTE_ADDR'] = $connection->getRemoteIp();
        $_SERVER['REMOTE_PORT'] = $connection->getRemotePort();

        $this->server = array_change_key_case ( $_SERVER ,  CASE_LOWER );
        $this->cookie = array_change_key_case ( $_COOKIE ,  CASE_LOWER );
        $this->request = array_change_key_case ( $_REQUEST ,  CASE_LOWER );
        $this->post = array_change_key_case ( $_POST ,  CASE_LOWER );
        $this->get = array_change_key_case ( $_GET ,  CASE_LOWER );
        $this->files = array_change_key_case ( $_FILES ,  CASE_LOWER );
        
        Http::sessionStart();

        $this->session = array_change_key_case ( $_SESSION ,  CASE_LOWER );
    }

    public function rawContent(){
        return $this->rawdata;
    }

    protected static function parseUploadFiles($http_body, $http_post_boundary)
    {
        $http_body           = substr($http_body, 0, strlen($http_body) - (strlen($http_post_boundary) + 4));
        $boundary_data_array = explode($http_post_boundary . "\r\n", $http_body);
        if ($boundary_data_array[0] === '') {
            unset($boundary_data_array[0]);
        }
        $key = -1;
        foreach ($boundary_data_array as $boundary_data_buffer) {
            list($boundary_header_buffer, $boundary_value) = explode("\r\n\r\n", $boundary_data_buffer, 2);
            // Remove \r\n from the end of buffer.
            $boundary_value = substr($boundary_value, 0, -2);
            $key ++;
            foreach (explode("\r\n", $boundary_header_buffer) as $item) {
                list($header_key, $header_value) = explode(": ", $item);
                $header_key = strtolower($header_key);
                switch ($header_key) {
                    case "content-disposition":
                        // Is file data.
                        if (preg_match('/name="(.*?)"; filename="(.*?)"$/', $header_value, $match)) {
                            // Parse $_FILES.
                            $_FILES[$key] = array(
                                'name' => $match[1],
                                'file_name' => $match[2],
                                'file_data' => $boundary_value,
                                'file_size' => strlen($boundary_value),
                            );
                            continue 2;
                        } // Is post field.
                        else {
                            // Parse $_POST.
                            if (preg_match('/name="(.*?)"$/', $header_value, $match)) {
                                $_POST[$match[1]] = $boundary_value;
                            }
                        }
                        break;
                    case "content-type":
                        // add file_type
                        $_FILES[$key]['file_type'] = trim($header_value);
                        break;
                }
            }
        }
    }

}