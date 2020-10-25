<?php
/**
 * Created by PhpStorm.
 * User: jesusslim
 * Date: 16/8/3
 * Time: 下午5:20
 */

namespace Partini\HttpContext;


class Output
{
    const HTTP_CONTINUE = 100;
    const HTTP_SWITCHING_PROTOCOLS = 101;
    const HTTP_PROCESSING = 102;            // RFC2518
    const HTTP_OK = 200;
    const HTTP_CREATED = 201;
    const HTTP_ACCEPTED = 202;
    const HTTP_NON_AUTHORITATIVE_INFORMATION = 203;
    const HTTP_NO_CONTENT = 204;
    const HTTP_RESET_CONTENT = 205;
    const HTTP_PARTIAL_CONTENT = 206;
    const HTTP_MULTI_STATUS = 207;          // RFC4918
    const HTTP_ALREADY_REPORTED = 208;      // RFC5842
    const HTTP_IM_USED = 226;               // RFC3229
    const HTTP_MULTIPLE_CHOICES = 300;
    const HTTP_MOVED_PERMANENTLY = 301;
    const HTTP_FOUND = 302;
    const HTTP_SEE_OTHER = 303;
    const HTTP_NOT_MODIFIED = 304;
    const HTTP_USE_PROXY = 305;
    const HTTP_RESERVED = 306;
    const HTTP_TEMPORARY_REDIRECT = 307;
    const HTTP_PERMANENTLY_REDIRECT = 308;  // RFC7238
    const HTTP_BAD_REQUEST = 400;
    const HTTP_UNAUTHORIZED = 401;
    const HTTP_PAYMENT_REQUIRED = 402;
    const HTTP_FORBIDDEN = 403;
    const HTTP_NOT_FOUND = 404;
    const HTTP_METHOD_NOT_ALLOWED = 405;
    const HTTP_NOT_ACCEPTABLE = 406;
    const HTTP_PROXY_AUTHENTICATION_REQUIRED = 407;
    const HTTP_REQUEST_TIMEOUT = 408;
    const HTTP_CONFLICT = 409;
    const HTTP_GONE = 410;
    const HTTP_LENGTH_REQUIRED = 411;
    const HTTP_PRECONDITION_FAILED = 412;
    const HTTP_REQUEST_ENTITY_TOO_LARGE = 413;
    const HTTP_REQUEST_URI_TOO_LONG = 414;
    const HTTP_UNSUPPORTED_MEDIA_TYPE = 415;
    const HTTP_REQUESTED_RANGE_NOT_SATISFIABLE = 416;
    const HTTP_EXPECTATION_FAILED = 417;
    const HTTP_I_AM_A_TEAPOT = 418;                                               // RFC2324
    const HTTP_MISDIRECTED_REQUEST = 421;                                         // RFC7540
    const HTTP_UNPROCESSABLE_ENTITY = 422;                                        // RFC4918
    const HTTP_LOCKED = 423;                                                      // RFC4918
    const HTTP_FAILED_DEPENDENCY = 424;                                           // RFC4918
    const HTTP_RESERVED_FOR_WEBDAV_ADVANCED_COLLECTIONS_EXPIRED_PROPOSAL = 425;   // RFC2817
    const HTTP_UPGRADE_REQUIRED = 426;                                            // RFC2817
    const HTTP_PRECONDITION_REQUIRED = 428;                                       // RFC6585
    const HTTP_TOO_MANY_REQUESTS = 429;                                           // RFC6585
    const HTTP_REQUEST_HEADER_FIELDS_TOO_LARGE = 431;                             // RFC6585
    const HTTP_UNAVAILABLE_FOR_LEGAL_REASONS = 451;
    const HTTP_INTERNAL_SERVER_ERROR = 500;
    const HTTP_NOT_IMPLEMENTED = 501;
    const HTTP_BAD_GATEWAY = 502;
    const HTTP_SERVICE_UNAVAILABLE = 503;
    const HTTP_GATEWAY_TIMEOUT = 504;
    const HTTP_VERSION_NOT_SUPPORTED = 505;
    const HTTP_VARIANT_ALSO_NEGOTIATES_EXPERIMENTAL = 506;                        // RFC2295
    const HTTP_INSUFFICIENT_STORAGE = 507;                                        // RFC4918
    const HTTP_LOOP_DETECTED = 508;                                               // RFC5842
    const HTTP_NOT_EXTENDED = 510;                                                // RFC2774
    const HTTP_NETWORK_AUTHENTICATION_REQUIRED = 511;                             // RFC6585

    protected $headers;
    protected $status;
    protected $cookies;
    protected $body;
    protected $context;

    public static $statusTexts = array(
        100 => 'Continue',
        101 => 'Switching Protocols',
        102 => 'Processing',
        200 => 'OK',
        201 => 'Created',
        202 => 'Accepted',
        203 => 'Non-Authoritative Information',
        204 => 'No Content',
        205 => 'Reset Content',
        206 => 'Partial Content',
        207 => 'Multi-Status',
        208 => 'Already Reported',
        226 => 'IM Used',
        300 => 'Multiple Choices',
        301 => 'Moved Permanently',
        302 => 'Found',
        303 => 'See Other',
        304 => 'Not Modified',
        305 => 'Use Proxy',
        307 => 'Temporary Redirect',
        308 => 'Permanent Redirect',
        400 => 'Bad Request',
        401 => 'Unauthorized',
        402 => 'Payment Required',
        403 => 'Forbidden',
        404 => 'Not Found',
        405 => 'Method Not Allowed',
        406 => 'Not Acceptable',
        407 => 'Proxy Authentication Required',
        408 => 'Request Timeout',
        409 => 'Conflict',
        410 => 'Gone',
        411 => 'Length Required',
        412 => 'Precondition Failed',
        413 => 'Payload Too Large',
        414 => 'URI Too Long',
        415 => 'Unsupported Media Type',
        416 => 'Range Not Satisfiable',
        417 => 'Expectation Failed',
        418 => 'I\'m a teapot',
        421 => 'Misdirected Request',
        422 => 'Unprocessable Entity',
        423 => 'Locked',
        424 => 'Failed Dependency',
        425 => 'Reserved for WebDAV advanced collections expired proposal',
        426 => 'Upgrade Required',
        428 => 'Precondition Required',
        429 => 'Too Many Requests',
        431 => 'Request Header Fields Too Large',
        451 => 'Unavailable For Legal Reasons',
        500 => 'Internal Server Error',
        501 => 'Not Implemented',
        502 => 'Bad Gateway',
        503 => 'Service Unavailable',
        504 => 'Gateway Timeout',
        505 => 'HTTP Version Not Supported',
        506 => 'Variant Also Negotiates (Experimental)',
        507 => 'Insufficient Storage',
        508 => 'Loop Detected',
        510 => 'Not Extended',
        511 => 'Network Authentication Required'
    );

    public function __construct(Context $ctx)
    {
        $this->context = $ctx;
        $this->setStatus(self::HTTP_OK);
        $this->body('');
    }

    public function header($k,$v){
        $this->headers[$k] = $v;
    }

    public function body($content){
        $this->body = $content;
        $this->header("Content-Length", strlen($content));
        return $this;
    }

    public function cookie($k,$v,...$params){
        $this->cookies[$k] = func_get_args();
    }

    public function json($content){
        $this->header('Content-Type', 'application/json');
        $this->body = json_encode($content);
    }

    public function jsonp($content){
        $this->header("Content-Type", "application/javascript; charset=utf-8");
        $cb = $this->context->input()->form('callback');
        if(empty($cb)) throw new ContextException("invalid callback");
        $this->body = " ".$cb."(".json_encode($content).");\r\n";
    }

    public function setStatus($status){
        $this->status = $status;
    }

    protected function cookieOutput(){
        if($this->cookies) foreach ($this->cookies as $k => $arr){
            $v = $arr[1];
            $path = empty($arr[3]) ? $this->context->getParent()->getConfig('COOKIE_PATH') : $arr[3];
            $domain = empty($arr[4]) ? $this->context->getParent()->getConfig('COOKIE_DOMAIN') : $arr[4];
            if(is_null($v)){
                //del
                setcookie($k,'',time()-3600,$path,$domain);
            }else{
                setcookie($k,$v,!empty($arr[2]) ? time()+intval($arr[2]) : 0,$path,$domain);
            }
        }
    }

    public function sendHeaders(){
        if(headers_sent()){
            return $this;
        }
        foreach ($this->headers as $k=>$v){
            header($k.': '.$v,false,$this->status);
        }
        // status
        header(sprintf('HTTP/%s %s %s', '1.0', $this->status, self::$statusTexts[$this->status]), true, $this->status);
        // cookie
        $this->cookieOutput();

        return $this;
    }

    public function sendBody(){
        echo $this->body;

        return $this;
    }

    public function send(){
        $this->sendHeaders()->sendBody()->closeBuffer();
        return $this;
    }

    public static function closeBuffer($flush = true){
        $status = ob_get_status(true);
        $num = count($status);
        while ($num-- > 0){
            if($flush){
                ob_end_flush();
            }else{
                ob_end_clean();
            }
        }
    }
}