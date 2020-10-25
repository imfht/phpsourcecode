<?php
class TableBase
{
    protected $table_name;
        
    static function getTablePrefix()
    {
        require(CL_ROOT . "/config/" . CL_CONFIG . "/config.php"); //define $db_prefix
        return empty($db_prefix) ? '' : trim($db_prefix);
    }
    
    function getTableName($with_prefix=true)
    {
        if (!$with_prefix)
        {           
            return $this->table_name;
        }
        return $this->getTablePrefix().$this->table_name;
    }
    
    function setTableName($table_name)
    {
       $this->table_name = $this->setString($table_name);
    }
}