<?php
namespace Smail;

use Smail\Mime\Nntp\newsGroupType;
use Smail\Mime\Nntp\messageType;
use Smail\Mime\Nntp\headerType;
use Smail\Util\ComFunc;

class Nntp
{

    private $stream = '';

    public function __construct()
    {}

    /**
     * connect to the nntp server
     *
     * @param string $nserver            
     * @param string $nport            
     */
    public function connect($nserver, $nport = 119)
    {
        if (empty($nserver)) {
            echo 'nntp server is empty';
            return false;
        }
        $stream = fsockopen($nserver, $nport, $errno, $errstr);
        if (! $stream) {
            echo 'open nntp connect failed' . $errstr . '**' . $errno;
            return false;
        }
        $weg = $this->lieszeile($stream);
        if (substr($weg, 0, 2) != "20") {
            echo $weg;
            return false;
        } else {
            fputs($stream, "mode reader\r\n");
            $weg = $this->lieszeile($stream);
            if (substr($weg, 0, 2) != "20" && (! $authorize || substr($weg, 0, 3) != "480")) {
                echo $weg;
                return false;
            }
        }
        $this->stream = $stream;
    }

    /**
     * auth with the nntp server
     *
     * @param string $user            
     * @param string $pass            
     */
    public function login($user, $pass)
    {
        if (! $this->stream) {
            echo 'please connect to the nntp server';
            return false;
        }
        fputs($this->stream, "authinfo user $user\r\n");
        $weg = $this->lieszeile($this->stream);
        fputs($this->stream, "authinfo pass $pass\r\n");
        $weg = lieszeile($this->stream);
        if (substr($weg, 0, 3) != "281") {
            echo $weg;
            return false;
        }
    }

    /**
     * fetch the nntp groups
     *
     * @param string $group_name            
     */
    public function groups($group_name)
    {
        if (! $this->stream) {
            echo 'please connect to the nntp server';
            return false;
        }
        $group = new newsGroupType();
        $group->set_name($group_name);
        fputs($this->stream, "xgtitle $group_name\r\n");
        $response = $this->liesZeile($this->stream);
        if (strcmp(substr($response, 0, 3), "282") == 0) {
            $desc = strrchr($response, "\t");
            if (strcmp($response, ".") == 0) {
                $desc = "-";
            }
        } else {
            $desc = $response;
        }
        if (strcmp(substr($response, 0, 3), "500") == 0) {
            $desc = "-";
        }
        if (strcmp($desc, "") == 0) {
            $desc = "-";
        }
        $group->set_description($desc);
        fputs($this->stream, "group " . $group_name . "\r\n");
        $response = $this->liesZeile($this->stream);
        $t = strchr($response, " ");
        $t = substr($t, 1, strlen($t) - 1);
        $gruppe->count = substr($t, 0, strpos($t, " "));
        if (strcmp(trim($group_name), "") != 0 && substr($group_name, 0, 1) != "#") {
            $newsgroups[] = $group;
        }
        $this->close();
        return $newsgroups;
    }

    /**
     * gets a list of aviable articles in the group
     *
     * @param string $group_name            
     */
    public function articles($group_name)
    {
        if (! $this->stream) {
            echo 'please connect to the nntp server';
            return false;
        }
        fputs($this->stream, "listgroup $group_name \r\n");
        $zeile = $this->lieszeile($this->stream);
        while (strcmp($zeile, ".") != 0) {
            $articleList[] = trim($zeile);
        }
        if (! isset($articleList)) {
            $articleList = "-";
        }
        return $articleList;
    }

    /**
     * make a article
     *
     * @param string $subject            
     * @param string $from            
     * @param string $newsgroups            
     * @param string $body            
     * @param string $references            
     * @param string $organization            
     * @param string $encoding            
     */
    private function make_article($subject, $from, $newsgroups, $body, $references = '', $organization = '', $encoding = '8bit')
    {
        if (! $this->stream) {
            echo 'please connect to the nntp server';
            return false;
        }
        fputs($this->stream, "post\r\n");
        $weg = $this->lieszeile($this->stream);
        if ($encoding == 'base64') {
            fputs($this->stream, "Content-Transfer-Encoding: base64\r\n");
            fputs($this->stream, 'Subject: ' . base64_encode(ComFunc::html2text($subject)) . "\r\n");
            if ($organization) {
                fputs($this->stream, 'Organization: ' . base64_encode($organization) . "\r\n");
            }
        } else {
            fputs($this->stream, "Content-Transfer-Encoding: 8bit\r\n");
            fputs($this->stream, 'Subject: ' . ComFunc::quoted_printable_encode(ComFunc::html2text($subject)) . "\r\n");
            if ($organization) {
                fputs($this->stream, 'Organization: ' . ComFunc::quoted_printable_encode($organization) . "\r\n");
            }
        }
        fputs($this->stream, 'From: ' . $from . "\r\n");
        fputs($this->stream, 'Newsgroups: ' . $newsgroups . "\r\n");
        fputs($this->stream, "Mime-Version: 1.0\r\n");
        fputs($this->stream, "Content-Type: text/plain; charset=UTF-8\r\n");
        fputs($this->stream, "User-Agent: NewsPortal 0.24pre9, http://florian-amrhein.de/newsportal/\r\n");
        fputs($this->stream, 'X-HTTP-Posting-Host: ' . gethostbyaddr(getenv("REMOTE_ADDR")) . "\r\n");
        if ($references) {
            fputs($this->stream, 'References: ' . $references . "\r\n");
        }
        $body = str_replace("\n.\r", "\n..\r", $body);
        $body = str_replace("\r", '', $body);
        $b = split("\n", ComFunc::html2text($body));
        $body = "";
        for ($i = 0; $i < count($b); $i ++) {
            if ((strpos(substr($b[$i], 0, strpos($b[$i], " ")), ">") != false) || (strcmp(substr($b[$i], 0, 1), ">") == 0)) {
                $body .= ComFunc::textwrap(stripSlashes($b[$i]), 78, "\r\n") . "\r\n";
            } else {
                $body .= ComFunc::textwrap(stripSlashes($b[$i]), 74, "\r\n") . "\r\n";
            }
        }
        if ($encoding == 'base64') {
            fputs($this->stream, "\r\n" . base64_encode(ComFunc::html2text($body)) . "\r\n.\r\n");
        } else {
            fputs($this->stream, "\r\n" . ComFunc::quoted_printable_encode(ComFunc::html2text($body)) . "\r\n.\r\n");
        }
    }

    /**
     * post an article
     *
     * @param string $subject            
     * @param string $from            
     * @param string $newsgroups            
     * @param string $body            
     * @param string $references            
     * @param string $organization            
     * @param string $encoding            
     */
    public function post_article($subject, $from, $newsgroups, $body, $references = '', $organization = '', $encoding = '8bit')
    {
        $this->make_article($subject, $from, $newsgroups, $body, $references, $organization, $encoding);
        $message = $this->lieszeile($this->stream);
        $this->close();
        return $message;
    }

    /**
     * cancle the artile
     *
     * @param string $subject            
     * @param string $from            
     * @param string $newsgroups            
     * @param string $body            
     * @param string $id            
     * @param string $references            
     * @param string $organization            
     * @param string $encoding            
     */
    public function cancle_article($subject, $from, $newsgroups, $body, $id, $references = '', $organization = '', $encoding = '8bit')
    {
        $this->make_article($subject, $from, $newsgroups, $body, $references, $organization, $encoding);
        fputs($this->stream, "Control: cancel " . $id . "\r\n");
        $message = $this->lieszeile($this->stream);
        $this->close();
        return $message;
    }

    /**
     * read the header of an article in plaintext into an array
     *
     * @param
     *            $group
     * @param $article_id can
     *            be the number of an article or its message-id.
     */
    public function article_header($group, $article_id)
    {
        if (! $this->stream) {
            echo 'please connect to the nntp server';
            return false;
        }
        fputs($this->stream, "group $group\r\n");
        fputs($this->stream, "head $article_id\r\n");
        $zeile = $this->lieszeile($this->stream);
        if (substr($zeile, 0, 3) != "221") {
            echo 'article not found';
            return false;
        } else {
            $body = "";
            while (strcmp(trim($zeile), ".") != 0) {
                $body .= $zeile . "\n";
            }
            return split("\n", str_replace("\r\n", "\n", $body));
        }
    }

    /**
     * read an article from the newsserver
     *
     * @param string $id
     *            the Message-ID of an article
     * @param string $bodynum
     *            the number of the attachment:
     *            -1: return only the header without any bodies or attachments.
     *            0: the body
     *            1: the first attachment
     * @param string $group            
     */
    public function article_detail($id, $bodynum = 0, $group = "")
    {
        if (! $this->stream) {
            echo 'please connect to the nntp server';
            return false;
        }
        $message = new messageType();
        if (! isset($message->header) || (! isset($message->body[$bodynum]) && $bodynum != - 1)) {
            if ($group != "") {
                fputs($this->stream, "group " . $group . "\r\n");
                $zeile = $this->lieszeile($this->stream);
            }
            fputs($this->stream, 'article ' . $id . "\r\n");
            $zeile = $this->lieszeile($this->stream);
            if (substr($zeile, 0, 3) != "220") {
                // requested article doesn't exist on the newsserver. Now we
                // should check, if the thread stored in the spool-directory
                // also doesnt't contain that article...
                echo 'the article is not exists';
                return false;
            }
            $rawmessage = array();
            $line = $this->lieszeile($this->stream);
            while (strcmp($line, ".") != 0) {
                $rawmessage[] = $line;
                $line = $this->lieszeile($this->stream);
            }
            $message = $this->parse_article($rawmessage);
            if (ereg('^[0-9]+$', $id)) {
                $message->header->number = $id;
            }
            $this->close();
            return $message;
        }
    }

    /**
     * Decode quoted-printable or base64 encoded headerlines
     *
     * @param
     *            $value
     */
    private function headerDecode($value)
    {
        if (eregi('=\?.*\?.\?.*\?=', $value)) { // is there anything encoded?
            if (eregi('=\?.*\?Q\?.*\?=', $value)) { // quoted-printable decoding
                $result1 = eregi_replace('(.*)=\?.*\?Q\?(.*)\?=(.*)', '\1', $value);
                $result2 = eregi_replace('(.*)=\?.*\?Q\?(.*)\?=(.*)', '\2', $value);
                $result3 = eregi_replace('(.*)=\?.*\?Q\?(.*)\?=(.*)', '\3', $value);
                $result2 = str_replace("_", " ", quoted_printable_decode($result2));
                $newvalue = $result1 . $result2 . $result3;
            }
            if (eregi('=\?.*\?B\?.*\?=', $value)) { // base64 decoding
                $result1 = eregi_replace('(.*)=\?.*\?B\?(.*)\?=(.*)', '\1', $value);
                $result2 = eregi_replace('(.*)=\?.*\?B\?(.*)\?=(.*)', '\2', $value);
                $result3 = eregi_replace('(.*)=\?.*\?B\?(.*)\?=(.*)', '\3', $value);
                $result2 = base64_decode($result2);
                $newvalue = $result1 . $result2 . $result3;
            }
            if (! isset($newvalue)) { // nothing of the above, must be an unknown encoding...
                $newvalue = $value;
            } else {
                $newvalue = $this->headerDecode($newvalue); // maybe there are more encoded
            }
            return $newvalue; // parts
        } else { // there wasn't anything encoded, return the original string
            return $value;
        }
    }

    /**
     * parse the nntp message header
     *
     * @param string $hdr            
     * @param string $number            
     */
    private function parse_header($hdr, $number = "")
    {
        for ($i = count($hdr) - 1; $i > 0; $i --)
            if (preg_match("/^(\x09|\x20)/", $hdr[$i]))
                $hdr[$i - 1] = $hdr[$i - 1] . " " . ltrim($hdr[$i]);
        $header = new headerType();
        $header->isAnswer = false;
        for ($count = 0; $count < count($hdr); $count ++) {
            $variable = substr($hdr[$count], 0, strpos($hdr[$count], " "));
            $value = trim(substr($hdr[$count], strpos($hdr[$count], " ") + 1));
            switch (strtolower($variable)) {
                case "from:":
                    $fromline = $this->address_decode($this->headerDecode($value), "nirgendwo");
                    if (! isset($fromline[0]["host"]))
                        $fromline[0]["host"] = "";
                    $header->from = $fromline[0]["mailbox"] . "@" . $fromline[0]["host"];
                    $header->username = $fromline[0]["mailbox"];
                    if (! isset($fromline[0]["personal"])) {
                        $header->name = "";
                    } else {
                        $header->name = $fromline[0]["personal"];
                    }
                    break;
                case "message-id:":
                    $header->id = $value;
                    break;
                case "subject:":
                    $header->subject = $this->headerDecode($value);
                    break;
                case "newsgroups:":
                    $header->newsgroups = $value;
                    break;
                case "organization:":
                    $header->organization = $value;
                    break;
                case "content-transfer-encoding:":
                    $header->content_transfer_encoding = trim(strtolower($value));
                    break;
                case "content-type:":
                    $header->content_type = array();
                    $subheader = split(";", $value);
                    $header->content_type[0] = strtolower(trim($subheader[0]));
                    for ($i = 1; $i < count($subheader); $i ++) {
                        $gleichpos = strpos($subheader[$i], "=");
                        if ($gleichpos) {
                            $subvariable = trim(substr($subheader[$i], 0, $gleichpos));
                            $subvalue = trim(substr($subheader[$i], $gleichpos + 1));
                            if ($subvalue[0] == '"' && $subvalue[strlen($subvalue) - 1] == '"')
                                $subvalue = substr($subvalue, 1, strlen($subvalue) - 2);
                            switch ($subvariable) {
                                case "charset":
                                    $header->content_type_charset = array(
                                        strtolower($subvalue)
                                    );
                                    break;
                                case "name":
                                    $header->content_type_name = array(
                                        $subvalue
                                    );
                                    break;
                                case "boundary":
                                    $header->content_type_boundary = $subvalue;
                            }
                        }
                    }
                    break;
                case "references:":
                    $ref = trim($value);
                    while (strpos($ref, "> <") != false) {
                        $header->references[] = substr($ref, 0, strpos($ref, " "));
                        $ref = substr($ref, strpos($ref, "> <") + 2);
                    }
                    $header->references[] = trim($ref);
                    break;
                case "date:":
                    $header->date = date('Y-m-d H:i:s', strtotime(trim($value)));
                    break;
                case "followup-to:":
                    $header->followup = trim($value);
                    break;
                case "x-newsreader:":
                case "x-mailer:":
                case "user-agent:":
                    $header->user_agent = trim($value);
                    break;
                case "x-face:": // not ready
                                // echo "<p>-".base64_decode($value)."-</p>";
                    break;
                case "x-no-archive:":
                    $header->xnoarchive = strtolower(trim($value));
            }
        }
        if (! isset($header->content_type[0]))
            $header->content_type[0] = "text/plain";
        if (! isset($header->content_transfer_encoding))
            $header->content_transfer_encoding = "8bit";
        if ($number != "")
            $header->number = $number;
        return $header;
    }

    /*
     * Split an internet-address string into its parts. An address string could
     * be for example:
     * - user@host.domain (Realname)
     * - "Realname" <user@host.domain>
     * - user@host.domain
     *
     * The address will be split into user, host (incl. domain) and realname
     *
     * $adrstring: The string containing the address in internet format
     * $defaulthost: The name of the host which should be returned if the
     * address-string doesn't contain a hostname.
     *
     * returns an hash containing the fields "mailbox", "host" and "personal"
     */
    private function address_decode($adrstring, $defaulthost)
    {
        $parsestring = trim($adrstring);
        $len = strlen($parsestring);
        $at_pos = strpos($parsestring, '@');
        $ka_pos = strpos($parsestring, "(");
        $kz_pos = strpos($parsestring, ')');
        $ha_pos = strpos($parsestring, '<');
        $hz_pos = strpos($parsestring, '>');
        $space_pos = strpos($parsestring, ')');
        $email = "";
        $mailbox = "";
        $host = "";
        $personal = "";
        if ($space_pos != false) {
            if ($ka_pos != false && $kz_pos != false) {
                $personal = substr($parsestring, $ka_pos + 1, $kz_pos - $ka_pos - 1);
                $email = trim(substr($parsestring, 0, $ka_pos - 1));
            }
        } else {
            $email = $adrstring;
        }
        if ($ha_pos != false && $hz_pos != false) {
            $email = trim(substr($parsestring, $ha_pos + 1, $hz_pos - $ha_pos - 1));
            $personal = substr($parsestring, 0, $ha_pos - 1);
        }
        if ($at_pos != false) {
            $mailbox = substr($email, 0, strpos($email, '@'));
            $host = substr($email, strpos($email, '@') + 1);
        } else {
            $mailbox = $email;
            $host = $defaulthost;
        }
        $personal = trim($personal);
        if (substr($personal, 0, 1) == '"')
            $personal = substr($personal, 1);
        if (substr($personal, strlen($personal) - 1, 1) == '"')
            $personal = substr($personal, 0, strlen($personal) - 1);
        $result["mailbox"] = trim($mailbox);
        $result["host"] = trim($host);
        if ($personal != "")
            $result["personal"] = $personal;
        return $result;
    }

    /**
     * parse the nntp message
     *
     * @param string $rawmessage            
     */
    private function parse_article($rawmessage)
    {
        $count_rawmessage = count($rawmessage);
        $message = new messageType();
        $rawheader = array();
        $i = 0;
        while ($rawmessage[$i] != "") {
            $rawheader[] = $rawmessage[$i];
            $i ++;
        }
        $message->header = $this->parse_header($rawheader);
        // Now we know if the message is a mime-multipart message:
        $content_type = split("/", $message->header->content_type[0]);
        if ($content_type[0] == "multipart") {
            $message->header->content_type = array();
            // We have multible bodies, so we split the message into its parts
            $boundary = "--" . $message->header->content_type_boundary;
            // lets find the first part
            while ($rawmessage[$i] != $boundary)
                $i ++;
            $i ++;
            $part = array();
            while ($i <= $count_rawmessage) {
                if ($rawmessage[$i] == $boundary || $i == $count_rawmessage - 1 || $rawmessage[$i] == $boundary . '--') {
                    $partmessage = $this->parse_article($part);
                    // merge the content-types of the message with those of the part
                    for ($o = 0; $o < count($partmessage->header->content_type); $o ++) {
                        $message->header->content_type[] = $partmessage->header->content_type[$o];
                        $message->header->content_type_charset[] = $partmessage->header->content_type_charset[$o];
                        $message->header->content_type_name[] = $partmessage->header->content_type_name[$o];
                        $message->body[] = $partmessage->body[$o];
                    }
                    $part = array();
                } else {
                    if ($i < $count_rawmessage)
                        $part[] = $rawmessage[$i];
                }
                if ($rawmessage[$i] == $boundary . '--')
                    break;
                $i ++;
            }
        } else { // No mime-attachments in the message:
            $body = "";
            $uueatt = 0; // as default we have no uuencoded attachments
            for ($i ++; $i < $count_rawmessage; $i ++) {
                // do we have an inlay uuencoded file?
                if (strtolower(substr($rawmessage[$i], 0, 5) != "begin") || $attachment_uudecode == false) {
                    $body .= $rawmessage[$i] . "\n";
                    // yes, it seems, we have!
                } else {
                    $old_i = $i;
                    $uue_infoline_raw = $rawmessage[$i];
                    $uue_infoline = explode(" ", $uue_infoline_raw);
                    $uue_data = "";
                    $i ++;
                    while ($rawmessage[$i] != "end") {
                        if (strlen(trim($rawmessage[$i])) > 2)
                            $uue_data .= $rawmessage[$i] . "\n";
                        $i ++;
                    }
                    // now write the data in an attachment
                    $uueatt ++;
                    $message->body[$uueatt] = uudecode($uue_data);
                    $message->header->content_type_name[$uueatt] = "";
                    for ($o = 2; $o < count($uue_infoline); $o ++)
                        $message->header->content_type_name[$uueatt] .= $uue_infoline[$o];
                    $message->header->content_type[$uueatt] = $this->get_mimetype_by_filename($message->header->content_type_name[$uueatt]);
                }
            }
            if ($message->header->content_type[0] == "text/plain") {
                $body = trim($body);
                if ($body == "")
                    $body = " ";
            }
            $body = $this->decode_body($body, $message->header->content_transfer_encoding);
            $message->body[0] = $body;
        }
        if (! isset($message->header->content_type_charset))
            $message->header->content_type_charset = array(
                "UTF-8"
            );
        if (! isset($message->header->content_type_name))
            $message->header->content_type_name = array(
                "unnamed"
            );
        for ($o = 0; $o < count($message->body); $o ++) {
            if (! isset($message->header->content_type_charset[$o]))
                $message->header->content_type_charset[$o] = "UTF-8";
            if (! isset($message->header->content_type_name[$o]))
                $message->header->content_type_name[$o] = "unnamed";
        }
        return $message;
    }

    /**
     * decode the text with the encoding
     *
     * @param string $body            
     * @param string $encoding            
     */
    private function decode_body($body, $encoding)
    {
        switch ($encoding) {
            case "base64":
                $body = base64_decode($body);
                break;
            case "quoted-printable":
                $body = quoted_printable_decode($body);
                $body = str_replace("=\n", "", $body);
        }
        return $body;
    }

    /**
     * returns the mimetype of an filename
     *
     * @param string $name            
     */
    private function get_mimetype_by_filename($name)
    {
        $ending = strtolower(strrchr($name, "."));
        switch ($ending) {
            case ".jpg":
                $type = "image/jpeg";
                break;
            case ".gif":
                $type = "image/gif";
                break;
            default:
                $type = "text/plain";
        }
        return $type;
    }

    /**
     * close the connecton with the nntp serverr
     */
    public function close()
    {
        if (! $this->stream) {
            echo 'please connect to the nntp server';
            return false;
        }
        fputs($this->stream, "quit\r\n");
        fclose($this->stream);
    }

    /**
     * read one line from the NNTP-server
     *
     * @param resource $stream            
     */
    private function lieszeile(&$stream)
    {
        if ($stream != false) {
            $t = str_replace("\n", "", str_replace("\r", "", fgets($stream, 1200)));
            return $t;
        }
    }
}