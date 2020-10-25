<?php
use ActiveRecord\Model;
class SystemGroups extends Model {
    static $table_name = 'admin_group';
    static $primary_key = 'gid';
}
