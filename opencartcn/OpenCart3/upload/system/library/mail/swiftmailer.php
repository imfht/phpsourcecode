<?php
/**
 * swiftmailer.php
 *
 * @copyright  2018 opencart.cn - All Rights Reserved
 * @link       http://www.guangdawangluo.com
 * @author     Edward Yang <yangjin@opencart.cn>
 * @created    2018-05-23 10:01
 * @modified   2018-05-23 10:01
 */

namespace Mail;

class SwiftMailer
{
    public function send()
    {
        $security = '';
        if ($this->smtp_port == 465) {
            $security = 'ssl';
            $this->smtp_hostname = str_replace(['ssl://', 'tls://'], '', $this->smtp_hostname);
        } elseif ($this->smtp_port == 587) {
            $security = 'tls';
            $this->smtp_hostname = str_replace(['ssl://', 'tls://'], '', $this->smtp_hostname);
        }

        $options['ssl']['verify_peer'] = false;
        $options['ssl']['verify_peer_name'] = false;
        if ($this->originAdaptor == 'smtp') {
            $transport = (new \Swift_SmtpTransport($this->smtp_hostname, $this->smtp_port, $security))
                ->setStreamOptions($options)
                ->setUsername($this->smtp_username)
                ->setPassword($this->smtp_password);
        } elseif ($this->originAdaptor == 'mail') {
            $transport = new \Swift_SendmailTransport('/usr/sbin/sendmail -bs');
        } else {
            throw new \Exception("Invalid adaptor {$this->originAdaptor} for SwiftMailer");
        }

        // Create the Mailer using your created Transport
        $mailer = new \Swift_Mailer($transport);

        // Create a message
        $message = (new \Swift_Message($this->subject))
            ->setSender($this->from, $this->sender)
            ->setFrom([$this->from => $this->sender])
            ->setTo([$this->to])
            ->setReplyTo($this->reply_to);

        if ($this->text) {
            $message->setContentType("text/plain")->setBody($this->text);
        } elseif ($this->html) {
            $message->setContentType("text/html")->setBody($this->html);
        }

        // Send the message
        $result = $mailer->send($message, $failures);
        return ['result' => $result, 'failures' => $failures];
    }
}
