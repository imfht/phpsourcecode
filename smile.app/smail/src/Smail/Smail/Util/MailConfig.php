<?php
namespace Smail\Util;

class MailConfig
{

    /**
     * 获取连接服务器的参数信息
     *
     * @param
     *            $username
     */
    public static function getConnectionPro($username)
    {
        $pro = array();
        $domain = substr($username, strpos($username, '@') + 1, strlen($username));
        $dir = dirname(dirname(__FILE__)) . "/";
        $xmlDoc = new \DOMDocument();
        $xmlDoc->load($dir.'Config/email_config.xml');
        $emailserver = $xmlDoc->getElementsByTagName("EMailServer");
        foreach ($emailserver as $config) {
            $mail_type = $config->getElementsByTagName("mailtype");
            $mail_type = $mail_type->item(0)->nodeValue;
            
            $mail_domain = $config->getElementsByTagName("mailname");
            $mail_domain = $mail_domain->item(0)->nodeValue;
            
            $smtp_server = $config->getElementsByTagName("smtpserver");
            $smtp_server = $smtp_server->item(0)->nodeValue;
            
            $smtp_port = $config->getElementsByTagName("smtpport");
            $smtp_port = $smtp_port->item(0)->nodeValue;
            
            $smtp_auth_mech = $config->getElementsByTagName("smtp_auth");
            $smtp_auth_mech = $smtp_auth_mech->item(0)->nodeValue;
            
            $imap_auth_mech = $config->getElementsByTagName("auth_mech");
            $imap_auth_mech = $imap_auth_mech->item(0)->nodeValue;
            
            $use_imap_tls = $config->getElementsByTagName("securityprotocol");
            $use_imap_tls = $use_imap_tls->item(0)->nodeValue;
            
            $imap_server_type = $config->getElementsByTagName("imap_server_type");
            $imap_server_type = $imap_server_type->item(0)->nodeValue;
            
            if ($mail_type == 'imap') {
                $imap_server = $config->getElementsByTagName("imapserver");
                $imap_server = $imap_server->item(0)->nodeValue;
                
                $imap_port = $config->getElementsByTagName("imapport");
                $imap_port = $imap_port->item(0)->nodeValue;
            } else {
                continue;
            }
            if ($use_imap_tls != 'none') {
                $use_imap_tls = true;
            } else {
                $use_imap_tls = false;
            }
            if ($domain == $mail_domain) {
                $pro[0] = $imap_auth_mech;
                $pro[1] = $use_imap_tls;
                $pro[2] = $imap_server;
                $pro[3] = $imap_port;
                $pro[4] = $smtp_server;
                $pro[5] = $smtp_port;
                $pro[6] = $mail_domain;
                $pro[7] = $smtp_auth_mech;
                $pro[8] = $imap_server_type;
                break;
            }
        }
        return $pro;
    }

    /**
     * 获取账户下的邮件夹
     *
     * @param unknown_type $mailType            
     */
    public static function getMailBoxes($mailType)
    {
        $xmlDoc = new DOMDocument();
        $xmlDoc->load('../config/box_config.xml');
        $boxes = $xmlDoc->getElementsByTagName("box");
        foreach ($boxes as $box) {
            $mail_type = $box->getElementsByTagName("type");
            $mail_type = $mail_type->item(0)->nodeValue;
            if ($mailType == $mail_type) {
                if ($mailType == 'sohu.com' || $mail_type == 'sogou.com' || $mail_type == 'qq.com') {
                    $boxname = $box->getElementsByTagName("boxname");
                    foreach ($boxname as $name) {
                        $inbox = $name->getElementsByTagName("inbox");
                        $inbox = $inbox->item(0)->nodeValue;
                        $drafts = $name->getElementsByTagName("drafts");
                        $drafts = $drafts->item(0)->nodeValue;
                        $sent = $name->getElementsByTagName("sent");
                        $sent = $sent->item(0)->nodeValue;
                        $trash = $name->getElementsByTagName("trash");
                        $trash = $trash->item(0)->nodeValue;
                        $names[] = $inbox;
                        $names[] = $drafts;
                        $names[] = $sent;
                        $names[] = $trash;
                        if ($mailType != 'qq.com') {
                            $junk = $name->getElementsByTagName("junk");
                            $junk = $junk->item(0)->nodeValue;
                            $names[] = $junk;
                        }
                        if ($mailType == '21cn.com') {
                            $ad = $name->getElementsByTagName("ad");
                            $ad = $ad->item(0)->nodeValue;
                            $ad = '&' . $ad . '-';
                            $names[] = $ad;
                        }
                        break;
                    }
                } elseif ($mail_type == 'gmail.com') {
                    $boxname = $box->getElementsByTagName("boxname");
                    foreach ($boxname as $name) {
                        $inbox = $name->getElementsByTagName("inbox");
                        $inbox = $inbox->item(0)->nodeValue;
                        
                        $drafts = $name->getElementsByTagName("drafts");
                        $drafts = $drafts->item(0)->nodeValue;
                        $drafts = parseGmailBoxName($drafts);
                        $sent = $name->getElementsByTagName("sent");
                        $sent = $sent->item(0)->nodeValue;
                        $sent = parseGmailBoxName($sent);
                        $trash = $name->getElementsByTagName("trash");
                        $trash = $trash->item(0)->nodeValue;
                        $trash = parseGmailBoxName($trash);
                        $junk = $name->getElementsByTagName("junk");
                        $junk = $junk->item(0)->nodeValue;
                        $junk = parseGmailBoxName($junk);
                        
                        $work = $name->getElementsByTagName("work");
                        $work = $work->item(0)->nodeValue;
                        $work = '&' . $work . '-';
                        $receipt = $name->getElementsByTagName("receipt");
                        $receipt = $receipt->item(0)->nodeValue;
                        $receipt = '&' . $receipt . '-';
                        $travel = $name->getElementsByTagName("travel");
                        $travel = $travel->item(0)->nodeValue;
                        $travel = '&' . $travel . '-';
                        $private = $name->getElementsByTagName("private");
                        $private = $private->item(0)->nodeValue;
                        $private = '&' . $private . '-';
                        
                        $flagged = $name->getElementsByTagName("flagged");
                        $flagged = $flagged->item(0)->nodeValue;
                        $flagged = parseGmailBoxName($flagged);
                        
                        $all = $name->getElementsByTagName("all");
                        $all = $all->item(0)->nodeValue;
                        $all = parseGmailBoxName($all);
                        
                        $important = $name->getElementsByTagName("all");
                        $important = $important->item(0)->nodeValue;
                        $important = parseGmailBoxName($important);
                        
                        $names[] = $inbox;
                        $names[] = $drafts;
                        $names[] = $sent;
                        $names[] = $trash;
                        $names[] = $junk;
                        $names[] = $work;
                        $names[] = $receipt;
                        $names[] = $travel;
                        $names[] = $private;
                        $names[] = $flagged;
                        $names[] = $all;
                        $names[] = $important;
                        
                        break;
                    }
                } else 
                    if ($mailType == '163.com' || $mailType == '126.com' || $mailType == 'yeah.net') {
                        $boxname = $box->getElementsByTagName("boxname");
                        foreach ($boxname as $name) {
                            $inbox = $name->getElementsByTagName("inbox");
                            $inbox = $inbox->item(0)->nodeValue;
                            $drafts = $name->getElementsByTagName("drafts");
                            $drafts = $drafts->item(0)->nodeValue;
                            $drafts = '&' . $drafts . '-';
                            $sent = $name->getElementsByTagName("sent");
                            $sent = $sent->item(0)->nodeValue;
                            $sent = '&' . $sent . '-';
                            $trash = $name->getElementsByTagName("trash");
                            $trash = $trash->item(0)->nodeValue;
                            $trash = '&' . $trash . '-';
                            $junk = $name->getElementsByTagName("junk");
                            $junk = $junk->item(0)->nodeValue;
                            $junk = '&' . $junk . '-';
                            $subscribe = $name->getElementsByTagName("subscribe");
                            $subscribe = $subscribe->item(0)->nodeValue;
                            $subscribe = '&' . $subscribe . '-';
                            $names[] = $inbox;
                            $names[] = $drafts;
                            $names[] = $sent;
                            $names[] = $trash;
                            $names[] = $junk;
                            $names[] = $subscribe;
                            if ($mailType != 'yeah.net') {
                                $virus = $name->getElementsByTagName("virus");
                                $virus = $virus->item(0)->nodeValue;
                                $virus = '&' . $virus . '-';
                                $ad = $name->getElementsByTagName("ad");
                                $ad = $ad->item(0)->nodeValue;
                                $ad = '&' . $ad . '-';
                                $names[] = $virus;
                                $names[] = $ad;
                            }
                            break;
                        }
                    } elseif ($mailType == 'sina.com') {
                        $boxname = $box->getElementsByTagName("boxname");
                        foreach ($boxname as $name) {
                            $inbox = $name->getElementsByTagName("inbox");
                            $inbox = $inbox->item(0)->nodeValue;
                            $drafts = $name->getElementsByTagName("drafts");
                            $drafts = $drafts->item(0)->nodeValue;
                            $drafts = '&' . $drafts . '-';
                            $sent = $name->getElementsByTagName("sent");
                            $sent = $sent->item(0)->nodeValue;
                            $sent = '&' . $sent . '-';
                            $trash = $name->getElementsByTagName("trash");
                            $trash = $trash->item(0)->nodeValue;
                            $trash = '&' . $trash . '-';
                            $junk = $name->getElementsByTagName("junk");
                            $junk = $junk->item(0)->nodeValue;
                            $junk = '&' . $junk . '-';
                            
                            $subscribe = $name->getElementsByTagName("subscribe");
                            $subscribe = $subscribe->item(0)->nodeValue;
                            $subscribe = '&' . $subscribe . '-';
                            
                            $star = $name->getElementsByTagName("star");
                            $star = $star->item(0)->nodeValue;
                            $star = '&' . $star . '-';
                            
                            $business = $name->getElementsByTagName("business");
                            $business = $business->item(0)->nodeValue;
                            $business = '&' . $business . '-';
                            
                            $webnotice = $name->getElementsByTagName("webnotice");
                            $webnotice = $webnotice->item(0)->nodeValue;
                            $webnotice = '&' . $webnotice . '-';
                            
                            $names[] = $inbox;
                            $names[] = $drafts;
                            $names[] = $sent;
                            $names[] = $trash;
                            $names[] = $junk;
                            $names[] = $subscribe;
                            $names[] = $star;
                            $names[] = $business;
                            $names[] = $webnotice;
                            break;
                        }
                    } elseif ($mailType == '21cn.com') {
                        $boxname = $box->getElementsByTagName("boxname");
                        foreach ($boxname as $name) {
                            $inbox = $name->getElementsByTagName("inbox");
                            $inbox = $inbox->item(0)->nodeValue;
                            $drafts = $name->getElementsByTagName("drafts");
                            $drafts = $drafts->item(0)->nodeValue;
                            $drafts = '&' . $drafts . '-';
                            $sent = $name->getElementsByTagName("sent");
                            $sent = $sent->item(0)->nodeValue;
                            $sent = '&' . $sent . '-';
                            $trash = $name->getElementsByTagName("trash");
                            $trash = $trash->item(0)->nodeValue;
                            $trash = '&' . $trash . '-';
                            $junk = $name->getElementsByTagName("junk");
                            $junk = $junk->item(0)->nodeValue;
                            $junk = '&' . $junk . '-';
                            
                            $ad = $name->getElementsByTagName("ad");
                            $ad = $ad->item(0)->nodeValue;
                            $ad = '&' . $ad . '-';
                            
                            $names[] = $inbox;
                            $names[] = $drafts;
                            $names[] = $sent;
                            $names[] = $trash;
                            $names[] = $junk;
                            $names[] = $ad;
                            break;
                        }
                    } else {
                        $boxname = $box->getElementsByTagName("boxname");
                        foreach ($boxname as $name) {
                            $inbox = $name->getElementsByTagName("inbox");
                            $inbox = $inbox->item(0)->nodeValue;
                            $drafts = $name->getElementsByTagName("drafts");
                            $drafts = $drafts->item(0)->nodeValue;
                            $sent = $name->getElementsByTagName("sent");
                            $sent = $sent->item(0)->nodeValue;
                            $trash = $name->getElementsByTagName("trash");
                            $trash = $trash->item(0)->nodeValue;
                            $junk = $name->getElementsByTagName("junk");
                            $junk = $junk->item(0)->nodeValue;
                            $subscribe = $name->getElementsByTagName("subscribe");
                            $subscribe = $subscribe->item(0)->nodeValue;
                            $names[] = $inbox;
                            $names[] = $drafts;
                            $names[] = $sent;
                            $names[] = $trash;
                            $names[] = $junk;
                            $names[] = $subscribe;
                            break;
                        }
                    }
                break;
            }
        }
        return $names;
    }

    /**
     * 获取邮件服务商信息
     *
     * @param
     *            $username
     */
    public static function getIsps()
    {
        $xmlDoc = new DOMDocument();
        $xmlDoc->load('../config/isp_config.xml');
        $isps = $xmlDoc->getElementsByTagName("isp");
        foreach ($isps as $key => $isp) {
            $name = $isp->getElementsByTagName("name");
            $name = $name->item(0)->nodeValue;
            $mail_domain = $isp->getElementsByTagName("domain");
            $mail_domain = $mail_domain->item(0)->nodeValue;
            $picpath = $isp->getElementsByTagName("picpath");
            $picpath = $picpath->item(0)->nodeValue;
            $result[$key]['name'] = $name;
            $result[$key]['domain'] = $mail_domain;
            $result[$key]['picpath'] = $picpath;
        }
        return $result;
    }

    public static function parseGmailBoxName($boxname)
    {
        $index = strpos($boxname, '/');
        $part1 = substr($boxname, 0, $index - 1);
        $part2 = substr($boxname, $index - 1, strlen($boxname));
        $part2 = '&' . $part2 . '-';
        $boxname = $part1 . $part2;
        return $boxname;
    }

    public static function init()
    {
        return array(
            'ATTACH_DIR' => 'C:/lib111',
            'DB_TYPE' => 'mysql',
            'DB_HOST' => '172.16.1.70',
            'DB_NAME' => 'smail',
            'DB_USER' => 'root',
            'DB_PWD' => 'mysql',
            'DB_PORT' => 3306,
            'DB_PREFIX' => 'ts_',
            'DB_CHARSET' => 'utf8'
        );
    }
}
