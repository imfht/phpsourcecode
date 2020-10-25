<?php
namespace Smail;

use Smail\MailBase;
use Smail\Mime\Rfc822Header;
use Smail\Mime\MessageHeader;
use Smail\Mime\Message;
use Smail\Util\MailConfig;
use Smail\Mime\ContentType;
use Smail\Mime\ComMime;
use Smail\Util\ComAuth;
use Smail\Util\ComFunc;

/**
 * smtp util class
 *
 * @author fuyou
 *        
 */
class Smtp extends MailBase
{

    /**
     * the sendbox name
     *
     * @var string
     */
    var $send_folder = '已发送';

    /**
     * open this you can move the email to the sendbox
     *
     * @var boolean
     */
    var $move_to_sent = false;

    /**
     * 构造函数
     *
     * @param String $username            
     * @param string $password            
     */
    public function __construct($username, $password)
    {
        $this->username = $username;
        $this->password = $password;
        $connection_pros = MailConfig::getConnectionPro($username);
        $this->use_tls = $connection_pros[1];
        $this->smtp_server = $connection_pros[4];
        $this->smtp_port = $connection_pros[5];
        $this->mail_domain = $connection_pros[6];
        $this->smtp_auth_mech = $connection_pros[7];
    }

    /**
     * create email
     *
     * @param
     *            $addresser
     * @param
     *            $to
     * @param
     *            $cc
     * @param
     *            $bcc
     * @param
     *            $mailprio
     * @param
     *            $subject
     * @param
     *            $body
     * @param
     *            $attach_file_name
     */
    private function smsmtp_create_message($addresser, $to, $cc, $bcc, $mailprio, $subject, $body, $attach_file_name = null)
    {
        $composeMessage = new Message();
        $rfc822_header = new Rfc822Header();
        $rfc822_header->from = $rfc822_header->parseAddress($addresser, true);
        $to = str_replace("\\", "", $to);
        $rfc822_header->to = $rfc822_header->parseAddress($to, true);
        $cc = str_replace("\\", "", $cc);
        $rfc822_header->cc = $rfc822_header->parseAddress($cc, true);
        $bcc = str_replace("\\", "", $bcc);
        $rfc822_header->bcc = $rfc822_header->parseAddress($bcc, true);
        $addresser = str_replace("\\", "", $addresser);
        $rfc822_header->reply_to = $rfc822_header->parseAddress($addresser, true);
        $rfc822_header->more_headers['Return-Receipt-To'] = $addresser;
        $rfc822_header->priority = $mailprio;
        $rfc822_header->subject = $subject;
        if (! empty($attach_file_name)) {
            $type = ComFunc::mime_type($attach_file_name);
            $composeMessage->initAttachment($type, $attach_file_name);
        }
        if (count($composeMessage->entities)) {
            $plain_body = new Message();
            $plain_text = base64_encode(strip_tags($body));
            $plain_body->body_part = $plain_text;
            $mime_header_plain = new MessageHeader();
            $mime_header_plain->type0 = 'text';
            $mime_header_plain->type1 = 'plain';
            $mime_header_plain->encoding = 'base64';
            $mime_header_plain->setTextParam('charset', 'utf-8');
            $plain_body->mime_header = $mime_header_plain;
            
            $html_body = new Message();
            $html_body->body_part = base64_encode($body);
            $mime_header_html = new MessageHeader();
            $mime_header_html->type0 = 'text';
            $mime_header_html->type1 = 'html';
            $mime_header_html->encoding = 'base64';
            $mime_header_html->setTextParam('charset', 'utf-8');
            $html_body->mime_header = $mime_header_html;
            
            $text_part = new Message();
            $mime_header = new MessageHeader();
            $mime_header->type0 = 'multipart';
            $mime_header->type1 = 'alternative';
            $text_part->setEnt("1");
            $text_part->addEntity($plain_body);
            $text_part->addEntity($html_body);
            $text_part->mime_header = $mime_header;
            
            array_unshift($composeMessage->entities, $text_part);
            $content_type = new ContentType('multipart/mixed');
        } else {
            $plain_body = new Message();
            $plain_text = base64_encode(strip_tags($body));
            $plain_body->body_part = $plain_text;
            $mime_header_plain = new MessageHeader();
            $mime_header_plain->type0 = 'text';
            $mime_header_plain->type1 = 'plain';
            $mime_header_plain->encoding = 'base64';
            $mime_header_plain->setTextParam('charset', 'utf-8');
            $plain_body->mime_header = $mime_header_plain;
            
            $html_body = new Message();
            $html_body->body_part = base64_encode($body);
            $mime_header_html = new MessageHeader();
            $mime_header_html->type0 = 'text';
            $mime_header_html->type1 = 'html';
            $mime_header_html->encoding = 'base64';
            $mime_header_html->setTextParam('charset', 'utf-8');
            $html_body->mime_header = $mime_header_html;
            
            // $text_part = new Message();
            // $mime_header = new MessageHeader();
            // $mime_header->type0 = 'multipart';
            // $mime_header->type1 = 'alternative';
            // $text_part->setEnt("1");
            // $text_part->addEntity($plain_body);
            // $text_part->addEntity($html_body);
            // $text_part->mime_header = $mime_header;
            
            // $composeMessage->addEntity($text_part);
            $composeMessage->addEntity($plain_body);
            $composeMessage->addEntity($html_body);
            $content_type = new ContentType('multipart/alternative');
        }
        $rfc822_header->content_type = $content_type;
        $composeMessage->rfc822_header = $rfc822_header;
        return $composeMessage;
    }

    /**
     * send email
     *
     * @param
     *            $to
     * @param
     *            $cc
     * @param
     *            $bcc
     * @param
     *            $mailprio
     * @param
     *            $subject
     * @param
     *            $body
     * @param
     *            $attach_file_name
     */
    public function send_message($to, $cc, $bcc, $mailprio, $subject, $body, $attach_file_name = null)
    {
        $message = $this->smsmtp_create_message($this->username, $to, $cc, $bcc, $mailprio, $subject, $body, $attach_file_name);
        $rfc822_header = $message->rfc822_header;
        $from = $rfc822_header->from[0];
        $to = $rfc822_header->to;
        $cc = $rfc822_header->cc;
        $bcc = $rfc822_header->bcc;
        $content_type = $rfc822_header->content_type;
        if ($content_type->type0 == 'multipart' && $content_type->type1 == 'report' && isset($content_type->properties['report-type']) && $content_type->properties['report-type'] == 'disposition-notification') {
            $from = new AddressStructure();
            $from->host = $_SERVER['HTTP_HOST'];
            $from->mailbox = '';
        }
        if ($this->use_tls == true && extension_loaded('openssl')) {
            $stream = @fsockopen('tls://' . $this->smtp_server, $this->smtp_port, $errorNumber, $errorString);
        } else {
            $stream = @fsockopen($this->smtp_server, $this->smtp_port, $errorNumber, $errorString);
        }
        if (! $stream) {
            $this->error = $errorString;
            return false;
        }
        $tmp = fgets($stream, 1024);
        if (! $this->smsmtp_errorcheck($tmp, $stream)) {
            return false;
        }
        if (isset($_SERVER['HTTP_HOST'])) {
            $HTTP_HOST = $_SERVER['HTTP_HOST'];
            if ($p = strrpos($HTTP_HOST, ':')) {
                $HTTP_HOST = substr($HTTP_HOST, 0, $p);
            }
            $helohost = $HTTP_HOST;
        } else {
            $helohost = '127.0.0.1';
        }
        if (preg_match('/^\d+\.\d+\.\d+\.\d+$/', $helohost)) {
            $helohost = '[' . $helohost . ']';
        }
        fputs($stream, "EHLO $helohost\r\n");
        $tmp = fgets($stream, 1024);
        if (! $this->smsmtp_errorcheck($tmp, $stream)) {
            fputs($stream, "HELO $helohost\r\n");
            $tmp = fgets($stream, 1024);
            if (! $this->smsmtp_errorcheck($tmp, $stream)) {
                return false;
            }
        }
        $this->smsmtp_auth($stream);
        $fromaddress = (strlen($from->mailbox) && $from->host) ? $from->mailbox . '@' . $from->host : '';
        if ($fromaddress) {
            fputs($stream, 'MAIL FROM:<' . $fromaddress . ">\r\n");
            $tmp = fgets($stream, 1024);
            if (! $this->smsmtp_errorcheck($tmp, $stream)) {
                return false;
            }
        }
        for ($i = 0, $cnt = count($to); $i < $cnt; $i ++) {
            if (! $to[$i]->host) {
                $to[$i]->host = '';
            }
            if (strlen($to[$i]->mailbox)) {
                fputs($stream, 'RCPT TO:<' . $to[$i]->mailbox . '@' . $to[$i]->host . ">\r\n");
                $tmp = fgets($stream, 1024);
                if (! $this->smsmtp_errorcheck($tmp, $stream)) {
                    return false;
                }
            }
        }
        for ($i = 0, $cnt = count($cc); $i < $cnt; $i ++) {
            if (! $cc[$i]->host) {
                $cc[$i]->host = '';
            }
            if (strlen($cc[$i]->mailbox)) {
                fputs($stream, 'RCPT TO:<' . $cc[$i]->mailbox . '@' . $cc[$i]->host . ">\r\n");
                $tmp = fgets($stream, 1024);
                if (! $this->smsmtp_errorcheck($tmp, $stream)) {
                    return false;
                }
            }
        }
        for ($i = 0, $cnt = count($bcc); $i < $cnt; $i ++) {
            if (! $bcc[$i]->host) {
                $bcc[$i]->host = '';
            }
            if (strlen($bcc[$i]->mailbox)) {
                fputs($stream, 'RCPT TO:<' . $bcc[$i]->mailbox . '@' . $bcc[$i]->host . ">\r\n");
                $tmp = fgets($stream, 1024);
                if (! $this->smsmtp_errorcheck($tmp, $stream)) {
                    return false;
                }
            }
        }
        fputs($stream, "DATA\r\n");
        $tmp = fgets($stream, 1024);
        if (! $this->smsmtp_errorcheck($tmp, $stream)) {
            return false;
        }
        $success = $this->mail($message, $stream);
        $this->finalizeStream($stream);
        if ($success && $this->move_to_sent) {
            $imap_stream = $this->smimap_login($this->username, $this->password);
            $success = $this->mail($message, $imap_stream, $this->send_folder);
            $this->smimap_logout($imap_stream);
        }
    }

    /**
     * smtp auth
     *
     * @param mixed $stream            
     */
    private function smsmtp_auth(&$stream)
    {
        if ($this->smtp_auth_mech == 'cram-md5' || $this->smtp_auth_mech == 'digest-md5') {
            if ($this->smtp_auth_mech == 'cram-md5') {
                fputs($stream, "AUTH CRAM-MD5\r\n");
            } elseif ($this->smtp_auth_mech == 'digest-md5') {
                fputs($stream, "AUTH DIGEST-MD5\r\n");
            }
            $tmp = fgets($stream, 1024);
            if (! $this->smsmtp_errorcheck($tmp, $stream)) {
                return false;
            }
            $chall = substr($tmp, 4);
            if ($this->smtp_auth_mech == 'cram-md5') {
                $response = ComAuth::cram_md5_response($this->username, $this->password, $chall);
            } elseif ($this->smtp_auth_mech == 'digest-md5') {
                $response = ComAuth::digest_md5_response($this->username, $this->password, $chall, 'smtp', $host);
            }
            fputs($stream, $response);
            $tmp = fgets($stream, 1024);
            if (! $this->smsmtp_errorcheck($tmp, $stream)) {
                return false;
            }
            if ($this->smtp_auth_mech == 'digest-md5') {
                fputs($stream, "\r\n");
                $tmp = fgets($stream, 1024);
                if (! $this->smsmtp_errorcheck($tmp, $stream)) {
                    return false;
                }
            }
        } elseif ($this->smtp_auth_mech == 'none') {
            fputs($stream, "AUTH LOGIN\r\n");
            $tmp = fgets($stream, 1024);
            if (! $this->smsmtp_errorcheck($tmp, $stream)) {
                return false;
            }
            fputs($stream, base64_encode($this->username) . "\r\n");
            $tmp = fgets($stream, 1024);
            if ($this->errorCheck($tmp, $stream)) {
                return false;
            }
            fputs($stream, base64_encode($this->password) . "\r\n");
            $tmp = fgets($stream, 1024);
            if (! $this->smsmtp_errorcheck($tmp, $stream)) {
                return false;
            }
        } elseif ($this->smtp_auth_mech == 'login') {
            fputs($stream, "AUTH LOGIN\r\n");
            $tmp = fgets($stream, 1024);
            if (! $this->smsmtp_errorcheck($tmp, $stream)) {
                return false;
            }
            fputs($stream, base64_encode($this->username) . "\r\n");
            $tmp = fgets($stream, 1024);
            if (! $this->smsmtp_errorcheck($tmp, $stream)) {
                return false;
            }
            fputs($stream, base64_encode($this->password) . "\r\n");
            $tmp = fgets($stream, 1024);
            if (! $this->smsmtp_errorcheck($tmp, $stream)) {
                return false;
            }
        } elseif ($this->smtp_auth_mech == "plain") {
            $auth = base64_encode("$this->username\0$this->username\0$this->password");
            $query = "AUTH PLAIN\r\n";
            fputs($stream, $query);
            $read = fgets($stream, 1024);
            if (substr($read, 0, 3) == '334') {
                fputs($stream, "$auth\r\n");
                $read = fgets($stream, 1024);
            }
            $results = explode(" ", $read, 3);
            $response = $results[1];
            $message = $results[2];
        } elseif ($this->smtp_auth_mech === 'saml_sasl') {
            $gz_compressed = false;
            $saml_assertion = 'MELLON_SAML_RESPONSE';
            if ($gz_compressed && function_exists('gzcompress')) {
                $saml_assertion = base64_encode(gzcompress(base64_decode($saml_assertion)));
            }
            $auth = base64_encode($this->username . "\0" . $saml_assertion);
            fputs($stream, "AUTH SAML\r\n");
            $tmp = fgets($stream, 1024);
            if ($this->smsmtp_errorcheck($tmp, $stream)) {
                return false;
            }
            fputs($stream, "$auth\r\n");
            $tmp = fgets($stream, 1024);
            if (! $this->smsmtp_errorcheck($tmp, $stream)) {
                return false;
            }
        } else {
            if (! $this->smsmtp_errorcheck("535 Unable to use this auth type", $stream)) {
                return false;
            }
        }
        return $stream;
    }

    /**
     * close the smtp stream
     *
     * @param
     *            $stream
     */
    private function finalizeStream(&$stream)
    {
        fputs($stream, "\r\n.\r\n");
        $tmp = fgets($stream, 1024);
        $this->smsmtp_errorcheck($tmp, $stream);
        fputs($stream, "QUIT\r\n");
        fclose($stream);
        return true;
    }

    /**
     * function send_mail - store the message to the Sent folder
     *
     * Overridden from parent class so that we can insert some
     * IMAP APPEND commands before and after the message is
     * sent on the IMAP stream.
     *
     * @param Message $message
     *            Message object to send
     * @param string $header
     *            Headers ready to send
     * @param string $boundary
     *            Message parts boundary
     * @param resource $stream
     *            Handle to the SMTP stream
     *            (when FALSE, nothing will be
     *            written to the stream; this can
     *            be used to determine the actual
     *            number of bytes that will be
     *            written to the stream)
     * @param
     *            int &$raw_length The number of bytes written (or that
     *            would have been written) to the
     *            output stream - NOTE that this is
     *            passed by reference
     * @param string $folder
     *            The IMAP folder to which the
     *            message is being sent
     *            
     * @return void
     *
     */
    private function imap_send_mail($message, $header, $boundary, $stream, &$raw_length, $folder)
    {
        $final_length = $raw_length;
        $this->writeBody($message, 0, $final_length, $boundary);
        if ($stream) {
            if (Com_dection::is_cn_code($folder)) {
                $folder = $mailbox = ComFunc::sm_mb_convert_encoding($folder, 'UTF7-IMAP', 'UTF-8');
            }
            $imap = new smail_imap();
            $imap->smimap_append($stream, $folder, $final_length);
            fputs($stream, $header);
            $this->writeBody($message, $stream, $raw_length, $boundary);
            $imap->smimap_append_done($stream, $folder);
            unset($imap);
        }
    }

    /**
     * send the message parts to the SMTP stream
     *
     * @param
     *            $message
     * @param
     *            $stream
     * @param
     *            $reply_id
     * @param
     *            $reply_ent_id
     * @param
     *            $imap_stream
     * @param
     *            $extra
     */
    private function mail(&$message, $stream, $extra = NULL)
    {
        $rfc822_header = $message->rfc822_header;
        if (count($message->entities)) {
            $boundary = $this->smsmtp_mimeBoundary();
            $rfc822_header->content_type->properties['boundary'] = '"' . $boundary . '"';
        } else {
            $boundary = '';
        }
        $raw_length = 0;
        $reply_rfc822_header = isset($message->reply_rfc822_header) ? $message->reply_rfc822_header : '';
        $header = $this->prepareRFC822_Header($rfc822_header, $reply_rfc822_header, $raw_length);
        if (! empty($extra)) {
            $this->imap_send_mail($message, $header, $boundary, $stream, $raw_length, $extra);
        } else {
            $this->smtp_send_mail($message, $header, $boundary, $stream, $raw_length);
        }
        return $raw_length;
    }

    /**
     * reply the message
     *
     * @param array $message            
     * @param int $reply_ent_id            
     */
    public function smsmtp_reply_message(&$message, $reply_ent_id, $mailbox)
    {
        $imap_stream = $this->smimap_login($this->username, $this->password);
        $this->smimap_mailbox_select($imap_stream, $mailbox);
        $reply_message = $this->smimap_get_message($imap_stream, $reply_id, $mailbox);
        $this->smimap_logout($imap_stream);
        if ($reply_ent_id) {
            $reply_message = $message->getEntity($reply_ent_id);
            $orig_header = $reply_message->rfc822_header;
        } else {
            $orig_header = $reply_message->rfc822_header;
        }
        $message->reply_rfc822_header = $orig_header;
        return $message;
    }

    /**
     * function prepareMIME_Header - creates the mime header
     *
     * @param Message $message
     *            Message object to act on
     * @param string $boundary
     *            mime boundary from fn MimeBoundary
     *            
     * @return string $header properly formatted mime header
     */
    private function prepareMIME_Header($message, $boundary, $default_charset = 'utf-8')
    {
        $mime_header = $message->mime_header;
        $rn = "\r\n";
        $header = array();
        $contenttype = 'Content-Type: ' . $mime_header->type0 . '/' . $mime_header->type1;
        if (count($message->entities)) {
            $contenttype .= ';' . ' boundary="' . $boundary . '"';
        }
        if (isset($mime_header->parameters['name'])) {
            $contenttype .= '; name="' . ComMime::encodeHeader($mime_header->parameters['name']) . '"';
        }
        if (isset($mime_header->parameters['charset'])) {
            $charset = $mime_header->parameters['charset'];
            $contenttype .= '; charset="' . ComMime::encodeHeader($charset) . '"';
        }
        if (isset($mime_header->textparam['charset'])) {
            $charset = $mime_header->textparam['charset'];
            $contenttype .= ';charset=' . $charset;
        }
        $header[] = $contenttype;
        if ($mime_header->description) {
            $header[] = 'Content-Description: ' . $mime_header->description . $rn;
        }
        if ($mime_header->encoding) {
            $encoding = $mime_header->encoding;
            $header[] = 'Content-Transfer-Encoding: ' . $mime_header->encoding . $rn;
        } else {
            if (! empty($message->att_local_name)) {
                $filename = $message->att_local_name;
                $file_has_long_lines = true;
            } else {
                $file_has_long_lines = FALSE;
            }
            if ($mime_header->type0 == 'multipart' || $mime_header->type1 == 'alternative') {} else 
                if (($mime_header->type0 == 'text' || $mime_header->type0 == 'message') && ! $file_has_long_lines) {
                    $header[] = 'Content-Transfer-Encoding: 8bit' . $rn;
                } else {
                    $header[] = 'Content-Transfer-Encoding: base64' . $rn;
                }
        }
        if ($mime_header->id) {
            $header[] = 'Content-ID: ' . $mime_header->id . $rn;
        }
        if ($mime_header->disposition) {
            $disposition = $mime_header->disposition;
            $contentdisp = 'Content-Disposition: ' . $disposition->name;
            if ($disposition->getProperty('filename')) {
                $contentdisp .= '; filename="' . ComMime::encodeHeader($disposition->getProperty('filename')) . '"';
            }
            $header[] = $contentdisp . $rn;
        }
        if ($mime_header->md5) {
            $header[] = 'Content-MD5: ' . $mime_header->md5 . $rn;
        }
        if ($mime_header->language) {
            $header[] = 'Content-Language: ' . $mime_header->language . $rn;
        }
        
        $cnt = count($header);
        $hdr_s = '';
        for ($i = 0; $i < $cnt; $i ++) {
            $hdr_s .= ComMime::foldLine($header[$i]);
        }
        $header = $hdr_s;
        $header .= $rn;
        return $header;
    }

    /**
     * 创建邮件头
     *
     * @param array $rfc822_header            
     * @param array $reply_rfc822_header            
     * @param int $raw_length            
     */
    private function prepareRFC822_Header(&$rfc822_header, &$reply_rfc822_header, &$raw_length)
    {
        if (! isset($_SERVER['SERVER_NAME'])) {
            $SERVER_NAME = 'smail.cn';
        } else {
            $SERVER_NAME = $_SERVER['SERVER_NAME'];
        }
        $REMOTE_ADDR = '';
        if (isset($_SERVER['REMOTE_ADDR'])) {
            $REMOTE_ADDR = $_SERVER['REMOTE_ADDR'];
        }
        $REMOTE_PORT = '';
        if (isset($_SERVER['REMOTE_PORT'])) {
            $REMOTE_PORT = $_SERVER['REMOTE_PORT'];
        }
        $REMOTE_HOST = '';
        if (isset($_SERVER['REMOTE_HOST'])) {
            $REMOTE_HOST = $_SERVER['REMOTE_HOST'];
        }
        $HTTP_VIA = '';
        if (isset($_SERVER['HTTP_VIA'])) {
            $HTTP_VIA = $_SERVER['HTTP_VIA'];
        }
        $HTTP_X_FORWARDED_FOR = '';
        if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $HTTP_X_FORWARDED_FOR = $_SERVER['HTTP_X_FORWARDED_FOR'];
        }
        $rn = "\r\n";
        if (empty($rfc822_header->message_id)) {
            $message_id = '<' . md5(ComFunc::generateRandomString(16, '', 7) . uniqid(mt_rand(), true)) . '.smail@' . $SERVER_NAME . '>';
        }
        if (isset($REMOTE_HOST)) {
            $received_from = "$REMOTE_HOST ([$REMOTE_ADDR])";
        } else {
            $received_from = $REMOTE_ADDR;
        }
        if (isset($HTTP_VIA) || isset($HTTP_X_FORWARDED_FOR)) {
            if (! isset($HTTP_X_FORWARDED_FOR) || $HTTP_X_FORWARDED_FOR == '') {
                $HTTP_X_FORWARDED_FOR = 'unknown';
            }
            $received_from .= " (proxying for $HTTP_X_FORWARDED_FOR)";
        }
        $header = array();
        if (! empty($rfc822_header->message_id)) {
            $header[] = 'Message-ID: ' . $rfc822_header->message_id . $rn;
        } else {
            $header[] = 'Message-ID: ' . $message_id . $rn;
            $rfc822_header->message_id = $message_id;
        }
        if (is_object($reply_rfc822_header) && $reply_rfc822_header->message_id) {
            $rep_message_id = $reply_rfc822_header->message_id;
            $header[] = 'In-Reply-To: ' . $rep_message_id . $rn;
            $rfc822_header->in_reply_to = $rep_message_id;
            $references = $this->calculate_references($reply_rfc822_header);
            $header[] = 'References: ' . $references . $rn;
            $rfc822_header->references = $references;
        }
        if (! empty($rfc822_header->date) && $rfc822_header->date != - 1) {
            $header[] = 'Date: ' . $rfc822_header->date . $rn;
        } else {
            date_default_timezone_set('PRC');
            $date = date(DATE_RFC2822);
            $header[] = "Date: $date" . $rn;
            $rfc822_header->date = $date;
        }
        $header[] = 'Subject: ' . ComMime::encodeHeader($rfc822_header->subject) . $rn;
        $header[] = 'From: ' . $rfc822_header->getAddr_s('from', ",$rn ", true) . $rn;
        if (count($rfc822_header->from) > 1) {
            $header[] = 'Sender: ' . $rfc822_header->getAddr_s('sender', ',', true) . $rn;
        }
        if (count($rfc822_header->to)) {
            $header[] = 'To: ' . $rfc822_header->getAddr_s('to', ",$rn ", true) . $rn;
        }
        if (count($rfc822_header->cc)) {
            $header[] = 'Cc: ' . $rfc822_header->getAddr_s('cc', ",$rn ", true) . $rn;
        }
        if (count($rfc822_header->reply_to)) {
            $header[] = 'Reply-To: ' . $rfc822_header->getAddr_s('reply_to', ',', true) . $rn;
        }
        $bcc = true;
        if (count($rfc822_header->bcc)) {
            $s = 'Bcc: ' . $rfc822_header->getAddr_s('bcc', ",$rn ", true) . $rn;
            if (! $bcc) {
                $raw_length += strlen($s);
            } else {
                $header[] = $s;
            }
        }
        $user_agent = 'smail';
        if (! empty($_SERVER['HTTP_USER_AGENT'])) {
            $user_agent = $_SERVER['HTTP_USER_AGENT'];
        }
        $header[] = 'User-Agent: ' . $user_agent . '0.1' . $rn;
        $header[] = 'MIME-Version: 1.0' . $rn;
        $contenttype = 'Content-Type: ' . $rfc822_header->content_type->type0 . '/' . $rfc822_header->content_type->type1;
        if (count($rfc822_header->content_type->properties)) {
            foreach ($rfc822_header->content_type->properties as $k => $v) {
                $contenttype .= ';' . $k . '=' . $v;
            }
        }
        $header[] = $contenttype . $rn;
        if ($encoding = $rfc822_header->encoding) {
            $header[] = 'Content-Transfer-Encoding: ' . $encoding . $rn;
        }
        if ($rfc822_header->dnt) {
            $dnt = $rfc822_header->getAddr_s('dnt');
            /* Pegasus Mail */
            $header[] = 'X-Confirm-Reading-To: ' . $dnt . $rn;
            /* RFC 2298 */
            $header[] = 'Disposition-Notification-To: ' . $dnt . $rn;
        }
        if ($rfc822_header->priority) {
            switch ($rfc822_header->priority) {
                case 1:
                    $header[] = 'X-Priority: 1 (Highest)' . $rn;
                    $header[] = 'Importance: High' . $rn;
                    break;
                case 3:
                    $header[] = 'X-Priority: 3 (Normal)' . $rn;
                    $header[] = 'Importance: Normal' . $rn;
                    break;
                case 5:
                    $header[] = 'X-Priority: 5 (Lowest)' . $rn;
                    $header[] = 'Importance: Low' . $rn;
                    break;
                default:
                    break;
            }
        }
        if (count($rfc822_header->more_headers)) {
            reset($rfc822_header->more_headers);
            foreach ($rfc822_header->more_headers as $k => $v) {
                $header[] = $k . ': ' . $v . $rn;
            }
        }
        $cnt = count($header);
        $hdr_s = '';
        for ($i = 0; $i < $cnt; $i ++) {
            $sKey = substr($header[$i], 0, strpos($header[$i], ':'));
            switch ($sKey) {
                case 'Message-ID':
                case 'In-Reply_To':
                    $hdr_s .= $header[$i];
                    break;
                case 'References':
                    $sRefs = substr($header[$i], 12);
                    $aRefs = explode(' ', $sRefs);
                    $sLine = 'References:';
                    foreach ($aRefs as $sReference) {
                        if (trim($sReference) == '') {} elseif (strlen($sLine) + strlen($sReference) > 76) {
                            $hdr_s .= $sLine;
                            $sLine = $rn . '    ' . $sReference;
                        } else {
                            $sLine .= ' ' . $sReference;
                        }
                    }
                    $hdr_s .= $sLine;
                    break;
                case 'To':
                case 'Cc':
                case 'Bcc':
                case 'From':
                    $hdr_s .= $header[$i];
                    break;
                default:
                    $hdr_s .= ComMime::foldLine($header[$i]);
                    break;
            }
        }
        $header = $hdr_s;
        $header .= $rn;
        $raw_length += strlen($header);
        return $header;
    }

    /**
     * function send_mail - send the message parts to the IMAP stream
     *
     * @param Message $message
     *            Message object to send
     * @param string $header
     *            Headers ready to send
     * @param string $boundary
     *            Message parts boundary
     * @param resource $stream
     *            Handle to the SMTP stream
     *            (when FALSE, nothing will be
     *            written to the stream; this can
     *            be used to determine the actual
     *            number of bytes that will be
     *            written to the stream)
     * @param
     *            int &$raw_length The number of bytes written (or that
     *            would have been written) to the
     *            output stream - NOTE that this is
     *            passed by reference
     * @param mixed $extra
     *            Any implementation-specific variables
     *            can be passed in here and used in
     *            an overloaded version of this method
     *            if needed.
     *            
     * @return void
     *
     */
    private function smtp_send_mail($message, $header, $boundary, &$stream, &$raw_length)
    {
        if ($stream) {
            fputs($stream, $header);
        }
        $this->writeBody($message, $stream, $raw_length, $boundary);
    }

    /**
     * function writeBody - generate and write the mime boundaries around each part to the stream
     *
     * Recursively formats and writes the MIME boundaries of the $message
     * to the output stream.
     *
     * @param Message $message
     *            Message object to transform
     * @param resource $stream
     *            SMTP output stream
     *            (when FALSE, nothing will be
     *            written to the stream; this can
     *            be used to determine the actual
     *            number of bytes that will be
     *            written to the stream)
     * @param
     *            integer &$length_raw raw length of the message (part)
     *            as returned by mail fn
     * @param string $boundary
     *            custom boundary to call, usually for subparts
     *            
     * @return void
     */
    private function writeBody($message, &$stream, &$length_raw, $boundary = '')
    {
        if ($boundary && $message->entity_id && count($message->entities)) {
            if (strpos($boundary, '_part_')) {
                $boundary = substr($boundary, 0, strpos($boundary, '_part_'));
            } elseif (strpos($boundary, '_trap_')) {
                $boundary = substr(strrev($boundary), 0, strpos(strrev($boundary), '_part_'));
            }
            $boundary_new = $boundary . '_part_' . $message->entity_id;
        } else {
            $boundary_new = $boundary;
        }
        if ($boundary && ! $message->rfc822_header) {
            $s = '--' . $boundary . "\r\n";
            $s .= $this->prepareMIME_Header($message, $boundary_new, $length_raw);
            $length_raw += strlen($s);
            if ($stream) {
                fputs($stream, $s);
            }
        }
        $this->writeBodyPart($message, $stream, $length_raw);
        $last = false;
        $entCount = count($message->entities);
        for ($i = 0; $i < $entCount; $i ++) {
            $msg = $this->writeBody($message->entities[$i], $stream, $length_raw, $boundary_new);
            if ($i == $entCount - 1)
                $last = true;
        }
        if ($boundary && $last) {
            $s = '--' . $boundary_new . "--\r\n" . "\r\n";
            $length_raw += strlen($s);
            if ($stream) {
                fputs($stream, $s);
            }
        }
    }

    /**
     * function writeBodyPart - write each individual mimepart to the message
     *
     * Recursively called by WriteBody to write each mime part to the SMTP stream
     *
     * @param Message $message
     *            Message object to transform
     * @param resource $stream
     *            SMTP output stream
     *            (when FALSE, nothing will be
     *            written to the stream; this can
     *            be used to determine the actual
     *            number of bytes that will be
     *            written to the stream)
     * @param
     *            integer &$length length of the message part
     *            as returned by mail fn
     *            
     * @return void
     */
    private function writeBodyPart($message, &$stream, &$length)
    {
        if ($message->mime_header) {
            $type0 = $message->mime_header->type0;
        } else {
            $type0 = $message->rfc822_header->content_type->type0;
        }
        $body_part_trailing = $last = '';
        switch ($type0) {
            case 'text':
                if ($message->body_part) {
                    $body_part = $message->body_part;
                    $body_part = str_replace("\0", '', $body_part);
                    $length += ComFunc::clean_crlf($body_part);
                    if ($stream) {
                        fputs($stream, $body_part);
                    }
                    $last = $body_part;
                } elseif ($message->att_local_name) {
                    $filename = $message->att_local_name;
                    $file = fopen($filename, 'rb');
                    $file_has_long_lines = true;
                    if ($file_has_long_lines) { // base64 encode
                        while ($tmp = fread($file, 570)) {
                            $body_part = chunk_split(base64_encode($tmp));
                            if (substr($body_part, - 1, 1) != "\n")
                                $body_part .= "\n";
                            $length += ComFunc::clean_crlf($body_part);
                            if ($stream) {
                                fputs($stream, $body_part);
                            }
                        }
                    } else { // 8bit encode
                        while ($body_part = fgets($file, 4096)) {
                            $length += ComFunc::clean_crlf($body_part);
                            if ($stream) {
                                fputs($stream, $body_part);
                            }
                            $last = $body_part;
                        }
                    }
                    fclose($file);
                }
                break;
            default:
                if ($message->body_part) {
                    $body_part = $message->body_part;
                    $length += ComFunc::clean_crlf($body_part);
                    if ($stream) {
                        fputs($stream, $body_part);
                    }
                } elseif ($message->att_local_name) {
                    $filename = $message->att_local_name;
                    $file = fopen($this->attach_dir . '/' . $filename, 'rb');
                    while ($tmp = fread($file, 570)) {
                        $body_part = chunk_split(base64_encode($tmp));
                        if (substr($body_part, - 1, 1) != "\n")
                            $body_part .= "\n";
                        $length += ComFunc::clean_crlf($body_part);
                        if ($stream) {
                            fputs($stream, $body_part);
                        }
                    }
                    fclose($file);
                }
                break;
        }
        $body_part_trailing = '';
        if ($last && substr($last, - 1) != "\n") {
            $body_part_trailing = "\r\n";
        }
        if ($body_part_trailing) {
            $length += strlen($body_part_trailing);
            if ($stream) {
                fputs($stream, $body_part_trailing);
            }
        }
    }

    /**
     * function calculate_references - calculate correct References string
     * Adds the current message ID, and makes sure it doesn't grow forever,
     * to that extent it drops message-ID's in a smart way until the string
     * length is under the recommended value of 1000 ("References: <986>\r\n").
     * It always keeps the first and the last three ID's.
     *
     * @param Rfc822Header $hdr
     *            message header to calculate from
     *            
     * @return string $refer concatenated and trimmed References string
     */
    private function calculate_references($hdr)
    {
        $aReferences = preg_split('/\s+/', $hdr->references);
        $message_id = ! empty($hdr->message_id) ? trim($hdr->message_id) : '';
        $in_reply_to = ! empty($hdr->in_reply_to) ? trim($hdr->in_reply_to) : '';
        if (count($aReferences) == 0 && $in_reply_to) {
            $aReferences[] = $in_reply_to;
        }
        $aReferences[] = $message_id;
        $aReferences = array_unique($aReferences);
        while (count($aReferences) > 4 && strlen(implode(' ', $aReferences)) >= 986) {
            $aReferences = array_merge(array_slice($aReferences, 0, 1), array_slice($aReferences, 2));
        }
        return implode(' ', $aReferences);
    }

    /**
     * create the boundry of the email bodu part
     */
    private function smsmtp_mimeBoundary()
    {
        static $mimeBoundaryString;
        if (! isset($mimeBoundaryString) || $mimeBoundaryString == '') {
            $mimeBoundaryString = '----=_Part_' . date('YmdHis') . '_' . mt_rand(10000, 99999) . '.1989';
        }
        return $mimeBoundaryString;
    }

    /**
     * check if an SMTP reply is an error
     * and set an error message
     *
     * @param
     *            $line
     * @param
     *            $smtpConnection
     */
    private function smsmtp_errorcheck($line, $stream)
    {
        $err_num = substr($line, 0, 3);
        $server_msg = substr($line, 4);
        while (substr($line, 0, 4) == ($err_num . '-')) {
            $line = fgets($stream, 1024);
            $server_msg .= substr($line, 4);
        }
        if (((int) $err_num{0}) < 4) {
            return true;
        }
        switch ($err_num) {
            case '421':
                $message = "Service not available, closing channel";
                break;
            case '432':
                $message = "A password transition is needed";
                break;
            case '450':
                $message = "Requested mail action not taken: mailbox unavailable";
                break;
            case '451':
                $message = "Requested action aborted: error in processing";
                break;
            case '452':
                $message = "Requested action not taken: insufficient system storage";
                break;
            case '454':
                $message = "Temporary authentication failure";
                break;
            case '500':
                $message = "Syntax error; command not recognized";
                break;
            case '501':
                $message = "Syntax error in parameters or arguments";
                break;
            case '502':
                $message = "Command not implemented";
                break;
            case '503':
                $message = "Bad sequence of commands";
                break;
            case '504':
                $message = "Command parameter not implemented";
                break;
            case '530':
                $message = "Authentication required";
                break;
            case '534':
                $message = "Authentication mechanism is too weak";
                break;
            case '535':
                $message = "Authentication failed";
                break;
            case '538':
                $message = "Encryption required for requested authentication mechanism";
                break;
            case '550':
                $message = "Requested action not taken: mailbox unavailable";
                break;
            case '551':
                $message = "User not local; please try forwarding";
                break;
            case '552':
                $message = "Requested mail action aborted: exceeding storage allocation";
                break;
            case '553':
                $message = "Requested action not taken: mailbox name not allowed";
                break;
            case '554':
                $message = "Transaction failed";
                break;
            default:
                $message = "Unknown response";
                break;
        }
        throw new Exception($message);
        // $this->error = $message . '=>' . nl2br(htmlspecialchars($server_msg));
        return false;
    }

    /**
     * pop auth
     *
     * @param
     *            $pop_server
     * @param
     *            $pop_port
     * @param
     *            $user
     * @param
     *            $pass
     */
    private function smsmtp_auth_pop($pop_server, $pop_por, $user, $pass)
    {
        if (! $pop_port) {
            $pop_port = 110;
        }
        if (! $pop_server) {
            $pop_server = 'localhost';
        }
        $popConnection = @fsockopen($pop_server, $pop_port, $err_no, $err_str);
        if (! $popConnection) {
            $this->error = "Error connecting to POP Server($pop_server:$pop_port)" . "$err_no:$err_str";
        } else {
            $tmp = fgets($popConnection, 1024); /* banner */
            if (substr($tmp, 0, 3) != '+OK') {
                return false;
            }
            fputs($popConnection, "USER $user\r\n");
            $tmp = fgets($popConnection, 1024);
            if (substr($tmp, 0, 3) != '+OK') {
                return false;
            }
            fputs($popConnection, 'PASS ' . $pass . "\r\n");
            $tmp = fgets($popConnection, 1024);
            if (substr($tmp, 0, 3) != '+OK') {
                return false;
            }
            fputs($popConnection, "QUIT\r\n");
            fclose($popConnection);
        }
    }
}