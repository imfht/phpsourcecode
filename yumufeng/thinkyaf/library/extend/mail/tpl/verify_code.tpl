<div id="mailContentContainer" class="qmbox qm_con_body_content qqmail_webmail_only">
    <style type="text/css">
        @media screen and (max-width: 525px) {
            .qmbox table[class=responsive-table] {
                width: 100% !important
            }

            .qmbox td[class=padding] {
                padding: 30px 8% 35px 8% !important
            }

            .qmbox td[class=padding2] {
                padding: 30px 4% 10px 4% !important;
                text-align: left
            }
        }

        @media all and (-webkit-min-device-pixel-ratio: 1.5) {
            .qmbox body[yahoo] .zhwd-high-res-img-wrap {
                background-size: contain;
                background-position: center;
                background-repeat: no-repeat
            }

            .qmbox body[yahoo] .zhwd-high-res-img-wrap img {
                display: none !important
            }
        }
    </style>
    <table border="0" cellpadding="0" cellspacing="0" width="100%">
        <tbody>
        <tr>
            <td bgcolor="#f7f9fa" align="center" style="padding:22px 0 20px 0" class="responsive-table">
                <table border="0" cellpadding="0" cellspacing="0"
                       style="background-color:f7f9fa;border-radius:3px;border:1px solid #dedede;margin:0 auto;background-color:#fff"
                       width="552" class="responsive-table">
                    <tbody>
                    <tr>
                        <td bgcolor="#64ba9d" height="54" align="center"
                            style="border-top-left-radius:3px;border-top-right-radius:3px">
                            <table border="0" cellpadding="0" cellspacing="0" width="100%">
                                <tbody>
                                <tr>
                                    <td align="center" class="zhwd-high-res-img-wrap zhwd-zhihu-logo">
                                        <?=$info['name'];?>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td bgcolor="#ffffff" align="center" style="padding:0 15px 0 15px">
                            <table border="0" cellpadding="0" cellspacing="0" width="480" class="responsive-table">
                                <tbody>
                                <tr>
                                    <td>
                                        <table width="100%" border="0" cellpadding="0" cellspacing="0">
                                            <tbody>
                                            <tr>
                                                <td>
                                                    <table cellpadding="0" cellspacing="0" border="0" align="left"
                                                           class="responsive-table">
                                                        <tbody>
                                                        <tr>
                                                            <td width="550" align="left" valign="top">
                                                                <table width="100%" border="0" cellpadding="0"
                                                                       cellspacing="0">
                                                                    <tbody>
                                                                    <tr>
                                                                        <td bgcolor="#ffffff" align="left"
                                                                            style="background-color:#fff;font-size:17px;color:#7b7b7b;padding:28px 0 0 0;line-height:25px">
                                                                            <b>尊敬的用户：<?=$email;?>，你好，</b>
                                                                        </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td align="left" valign="top"
                                                                            style="font-size:14px;color:#7b7b7b;line-height:25px;font-family:Hiragino Sans GB;padding:20px 0 20px 0">
                                                                            你此次操作的验证码如下，请在 10 分钟内输入验证码进行下一步操作。
                                                                            如非你本人操作，请忽略此邮件。

                                                                        </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td style="border-bottom:1px #f1f4f6 solid;padding:0 0 40px 0"
                                                                            align="center" class="padding">
                                                                            <table border="0" cellspacing="0"
                                                                                   cellpadding="0"
                                                                                   class="responsive-table">
                                                                                <tbody>
                                                                                <tr>
                                                                                    <td>
															<span style="font-family:Hiragino Sans GB">
															<div style="padding:10px 18px 10px 18px;border-radius:3px;text-align:center;text-decoration:none;background-color:#ecf4fb;font-size:20px;font-weight:700;letter-spacing:2px;margin:0;white-space:nowrap">
																<span><?=$number;?></span>
															</div>
															</span>
                                                                                    </td>
                                                                                </tr>
                                                                                </tbody>
                                                                            </table>
                                                                        </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td align="left" valign="top"
                                                                            style="font-size:14px;color:#7b7b7b;line-height:25px;font-family:Hiragino Sans GB;padding:20px 0 20px 0">

                                                                            请不要将验证码泄露给任何人（工作人员不会向您索取此验证码！)
                                                                        </td>
                                                                    </tr>
                                                                    </tbody>
                                                                </table>
                                                            </td>
                                                        </tr>
                                                        </tbody>
                                                    </table>
                                                </td>
                                            </tr>
                                            </tbody>
                                        </table>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </td>
        </tr>
        </tbody>
    </table>
    <table cellpadding="0" cellspacing="0" border="0" width="100%">
        <tbody>
        <tr>
            <td bgcolor="#f7f9fa" align="center">
                <table width="552" border="0" cellpadding="0" cellspacing="0" align="center" class="responsive-table">
                    <tbody>
                    <tr>
                        <td align="center" valign="top" bgcolor="#f7f9fa"
                            style="font-family:Hiragino Sans GB;font-size:12px;color:#b6c2cc;line-height:17px;padding:0 0 25px 0">
                            这封邮件的收件地址是 <a href="mailto:<?=$email;?>" target="_blank"><?=$email;?></a><br>
                            © 2016 - <?=date('Y');?> <?=$info['name'];?>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </td>
        </tr>
        </tbody>
    </table>
    <style type="text/css">
        .qmbox style, .qmbox script, .qmbox head, .qmbox link, .qmbox meta {
            display: none !important
        }
    </style>
</div>