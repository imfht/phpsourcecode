<?php
if (is_file($t_path . self_name())) {
  include $t_path . self_name();
} else {
  die($_lang['tpl_error']);
}
