<?php

if (!isset($this->req->id) || strlen($this->req->id) == 0) {
    die("The instance does not exist");
}

use LessPHP\LessKeeper\Keeper;
$kpr = new Keeper();

$info = $kpr->NodeGet("/h5db/info/{$this->req->id}");
$info = json_decode($info, true);

$struct = $kpr->NodeGet("/h5db/struct/{$this->req->id}");
$struct = json_decode($struct, true);
function _struct_dismap($k)
{
    $v = 'Unknow';
    switch ($k) {
        case 'ft_varchar':
            $v = '字符串 (varchar)';
            break;
        case 'ft_string':
            $v = '文本 (text)';
            break;
        case 'ft_int':
            $v = '整数 (int)';
            break;
        case 'ft_timestamp':
            $v = 'Unix 时间 (int)';
            break;
        case 'ft_blob':
            $v = '二进制';
            break;
        default:
            # code...
            break;
    }
    return $v;
}
?>

<div style="padding:10px;content:'Ex';">
  <table width="100%" style="padding:5px;">
    <tr>
        <td>Name</td>
        <td><?php echo $info['title']?></td>
    </tr>
    <tr>
        <td width="120px">Instance ID</td>
        <td><?php echo $this->req->id?></td>
    </tr>
    <tr>
        <td valign="top">
            Structure
        </td>
        <td>
          <table class="table table-hover" width="100%">
            <tr>
                <td>Column</td>
                <td>Type</td>
                <td></td>
            </tr>
            <?php
            foreach ($struct as $k => $v) {
                $checked = '';
                if ($v['i'] == 1) {
                    $checked = '<i class="/lesscreator/static/img/accept.png"></i>';
                }
                ?>
                <tr>
                    <td><?=$v['n']?></td>
                    <td>
                        <?php 
                        echo _struct_dismap($v['t']);
                        if (intval($v['l']) > 0) {
                            echo " ({$v['l']})";
                        }
                        ?>
                    </td>
                    <td><?php echo $checked?></td>
                </tr>
                <?php
            }
            ?>            
          </table>
        </td>
    </tr>
    <tr>
        <td></td>
        <td><input type="submit" class="btn" value="Edit" /></td>
    </tr>
  </table>
</div>

<script>

</script>
