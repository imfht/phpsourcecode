<?php
/**
 * @package        OpenCart
 * @author        Daniel Kerr
 * @copyright    Copyright (c) 2005 - 2017, OpenCart, Ltd. (https://www.opencart.com/)
 * @license        https://opensource.org/licenses/GPL-3.0
 * @link        https://www.opencart.com
 */

/**
 * Mail class
 */
class Mail
{
    protected $to;
    protected $from;
    protected $sender;
    protected $reply_to;
    protected $subject;
    protected $text;
    protected $html;
    protected $attachments = array();
    public $parameter;
    private $logger;
    private $adaptorText;
    private $originAdaptor;

    /**
     * Constructor
     *
     * @param    string $adaptor
     * @throws Exception
     */
    public function __construct($adaptor = '')
    {
        if (empty($adaptor)) {
            $adaptor = config('config_mail_engine');
        }

        $this->originAdaptor = $adaptor;
        if ($adaptor == 'mail' || $adaptor == 'smtp') {
            $adaptor = 'SwiftMailer';
        }
        $this->adaptorText = snake_case($adaptor);
        $class = 'Mail\\' . $adaptor;

        if (class_exists($class)) {
            $this->adaptor = new $class();
        } else {
            trigger_error('Error: Could not load mail adaptor ' . $adaptor . '!');
            exit();
        }
        $this->setParams();
        $this->logger = new \Log("mail/{$this->adaptorText}.log");
        $this->logger->write("=====================================================");
        $this->logger->write("Start log for {$this->adaptorText}:");
    }

    private function setParams()
    {
        $config = registry('config');
        $this->parameter = $config->get('mail_sendmail_parameter');
        $this->smtp_hostname = $config->get('mail_smtp_hostname');
        $this->smtp_username = $config->get('mail_smtp_username');
        $this->smtp_password = html_entity_decode($config->get('mail_smtp_password'), ENT_QUOTES, 'UTF-8');
        $this->smtp_port = $config->get('mail_smtp_port');
        $this->smtp_timeout = $config->get('mail_smtp_timeout');

        if (!$this->from) {
            $this->setFrom($config->get('config_email'));
        }

        if (!$this->sender) {
            $storeName = html_entity_decode($config->get('config_name'), ENT_QUOTES, 'UTF-8');
            $this->setSender($storeName);
        }
    }

    /**
     *
     *
     * @param    mixed $to
     */
    public function setTo($to)
    {
        $this->to = $to;
    }

    /**
     *
     *
     * @param    string $from
     */
    public function setFrom($from)
    {
        $this->from = $from;
    }

    /**
     *
     *
     * @param    string $sender
     */
    public function setSender($sender)
    {
        $this->sender = $sender;
    }

    /**
     *
     *
     * @param    string $reply_to
     */
    public function setReplyTo($reply_to)
    {
        $this->reply_to = $reply_to;
    }

    /**
     *
     *
     * @param    string $subject
     */
    public function setSubject($subject)
    {
        $this->subject = $subject;
    }

    /**
     *
     *
     * @param    string $text
     */
    public function setText($text)
    {
        $this->text = $text;
    }

    /**
     *
     *
     * @param    string $html
     */
    public function setHtml($html)
    {
        $this->html = $html;
    }

    /**
     *
     *
     * @param    string $filename
     */
    public function addAttachment($filename)
    {
        $this->attachments[] = $filename;
    }

    /**
     *
     *
     */
    public function send()
    {
        try {
            $this->logParameters();
            if (!$this->to) {
                throw new \Exception('Error: E-Mail to required!');
            }

            if (!$this->from) {
                throw new \Exception('Error: E-Mail from required!');
            }

            if (!$this->sender) {
                throw new \Exception('Error: E-Mail sender required!');
            }

            if (!$this->subject) {
                throw new \Exception('Error: E-Mail subject required!');
            }

            if ((!$this->text) && (!$this->html)) {
                throw new \Exception('Error: E-Mail message required!');
            }

            foreach (get_object_vars($this) as $key => $value) {
                $this->adaptor->$key = $value;
            }

            $result = $this->adaptor->send();
            $this->logResult($result);
        } catch (\Exception $e) {
            $this->logger = new \Log("mail/{$this->adaptorText}_error.log");
            $this->logger->write($e->getMessage());
        }
    }

    private function logParameters()
    {
        $this->logger->write("Parameters for {$this->adaptorText}:");
        foreach (get_object_vars($this) as $key => $value) {
            $value = \json_encode($value);
            $this->logger->write("$key : $value");
        }
    }

    private function logResult($result)
    {
        $this->logger->write("Result for {$this->adaptorText}:");
        $this->logger->write($result);
        $this->logger->write("\r\n\r\n\r\n");
    }
}
