<?php
class Sql {

  public static function parse($sql) {
    if(@DATA_PREFIX) {
      if (strpos($sql, ' ' . DATA_PREFIX)===false) {

        if (strpos($sql, 'FROM ')!==false) {
          if (strpos($sql, 'AS ')!==false) {
            $val = preg_replace('/`?([a-z_]*)`?\sAS\s/', "`" . DATA_PREFIX . "$1` AS ", $sql);
          } else {
            $val = preg_replace('/FROM\s`?([a-z_]*)`?/', "FROM `" . DATA_PREFIX . "$1`", $sql);
          }
        }

        elseif (strpos($sql, 'INSERT INTO ')!==false) {
          $val = preg_replace('/INSERT\sINTO\s`?([a-z_]*)`?/', "INSERT INTO `" . DATA_PREFIX . "$1`", $sql);
        }

        elseif (strpos($sql, 'UPDATE ')!==false) {
          $val = preg_replace('/UPDATE\s`?([a-z_]*)`?/', "UPDATE `" . DATA_PREFIX . "$1`", $sql);
        }

        elseif (strpos($sql, 'LOCK ')!==false) {
          $val = preg_replace('/LOCK\sTABLES\s`?([a-z_]*)`?/', "LOCK TABLES `" . DATA_PREFIX . "$1`", $sql);
        }

        elseif (strpos($sql, 'REPAIR ')!==false) {
          $val = preg_replace('/REPAIR\sTABLE\s`?([a-z_]*)`?/', "REPAIR TABLE `" . DATA_PREFIX . "$1`", $sql);
        }

        elseif (strpos($sql, 'OPTIMIZE ')!==false) {
          $val = preg_replace('/OPTIMIZE\sTABLE\s`?([a-z_]*)`?/', "OPTIMIZE TABLE `" . DATA_PREFIX . "$1`", $sql);
        }

        elseif (strpos($sql, 'TRUNCATE')!==false) {
          $val = preg_replace('/TRUNCATE\sTABLE\s`?([a-z_]*)`?/', "TRUNCATE TABLE `" . DATA_PREFIX . "$1`", $sql);
        }

        elseif (strpos($sql, 'DROP ')!==false) {
          if (strpos($sql, 'DROP TABLE IF EXISTS')!==false) {
            $val = preg_replace('/DROP\sTABLE\sIF\sEXISTS\s`?([a-z_]*)`?/', "DROP TABLE IF EXISTS `" . DATA_PREFIX . "$1`", $sql);
          } else {
            $val = preg_replace('/DROP\sTABLE\s`?([a-z_]*)`?/', "DROP TABLE `" . DATA_PREFIX . "$1`", $sql);
          }
        }

        elseif (strpos($sql, 'CREATE TABLE ')!==false) {
          $val = preg_replace('/CREATE\sTABLE\s`?([a-z_]*)`?/', "CREATE TABLE `" . DATA_PREFIX . "$1`", $sql);
        }

        elseif (strpos($sql, 'ALTER TABLE ')!==false) {
          $val = preg_replace('/ALTER\sTABLE\s`?([a-z_]*)`?/', "ALTER TABLE `" . DATA_PREFIX . "$1`", $sql);
        }

        else {
          $val = $sql;
        }

        return $val;
      } else {
        return $sql;
      }
    } else {
      return $sql;
    }
  }

}