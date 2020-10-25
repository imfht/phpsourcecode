
<div style="width:338px; text-align:left;">

    <table width="100%" border="0" cellpadding="0" cellspacing="0" class="box" style="margin-bottom:8px;">
        <tbody><tr>
            <?
            $psql = "select parentid from ok3w_class where channelid=1 and id=" . $classid;
            $parentid = $db->get_one($psql);

            $sql="select id,sortname,gotourl from ok3w_class where channelid=1 and parentid=".$parentid['parentid'] ." and isnav=1 order by orderid";
            $result = $db->query($sql);
            $i=0;

            while ($info = $db->fetch_array($result))
            {
                $page_url = "/list.php?id=" . $info["id"];
                $showtitle = $info["sortname"];

                ?>   <td style="padding:8px;">
                <div class="a_class"><a href="<?=$page_url?>"><?=$showtitle?></a></div>
            </td>
                <?
                if($i%2==1) echo ' <td width="8"></td></tr><tr>';
                $i++;
            }
            ?>
        </tr>
        </tbody>
    </table>

    <table border="0" cellspacing="0" cellpadding="0" class="dragTable" width="100%">
        <tbody><tr>
            <td class="head"><h3 class="L"></h3><span class="TAG">最近更新</span></td>
        </tr>
        <tr>
            <td class="middle"><table width="100%" border="0" cellspacing="0" cellpadding="0" style="margin:5px 0px 5px 0px;">
                    <tbody><tr>
                        <td>
                            <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                <tbody>

                                <?
                                $psql="select id,classid,title,titlecolor,titleurl,addtime,hits from ok3w_article where channelid=1 and ispass=1 and isdelete=0";
                                $psql.=" order by addtime desc,id desc limit 0,15";
                                $result = $db->query($psql);
                                while($info = $db->fetch_array($result))
                                {
                                    $page_url="/show.php?id=".$info["id"];
                                    $showtitle=$info["title"];

                                    ?>
                                    <tr>
                                        <td class="list_title"><a href="<?=$page_url?>" target="_blank"><?=$showtitle?></a>
                                        </td>
                                    </tr>
                                <?
                                }
                                ?>
                                </tbody>
                            </table></td>
                    </tr>
                    </tbody></table></td>
        </tr>
        </tbody></table>

    <table border="0" cellspacing="0" cellpadding="0" class="dragTable" width="100%">
        <tbody><tr>
            <td class="head"><h3 class="L"></h3>
                <span class="TAG">点击排行</span></td>
        </tr>
        <tr>
            <td class="middle"><table width="100%" border="0" cellspacing="0" cellpadding="0" style="margin:5px 0px 5px 0px;">
                    <tbody><tr>
                        <td>
                            <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                <tbody>

                                <?
                                $psql="select id,classid,title,titlecolor,titleurl,addtime,hits from ok3w_article where channelid=1 and ispass=1 and isdelete=0";
                                $psql.="  order by hits desc,addtime desc,id desc limit 0,15";
                                $result = $db->query($psql);
                                while($info = $db->fetch_array($result))
                                {
                                    $page_url="/show.php?id=".$info["id"];
                                    $showtitle=$info["title"];

                                    ?>
                                    <tr>
                                        <td class="list_title"><a href="<?=$page_url?>" target="_blank"><?=$showtitle?></a>
                                        </td>
                                    </tr>
                                <?
                                }
                                ?>


                                </tbody>
                            </table></td>
                    </tr>
                    </tbody></table></td>
        </tr>
        </tbody></table>
</div>
