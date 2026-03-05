<?php
class Core_Model_Resource_Abstract
{
    protected $_tableName = null;
    protected $_primaryKay = null;

    protected function _init($tableName, $primaryKey)
    {
        $this->_tableName = $tableName;
        $this->_primaryKay = $primaryKey;
    }

    public function getTableName()
    {
        return $this->_tableName;
    }

    public function getPrimaryKey()
    {
        return $this->_primaryKay;
    }

    public function load($model, $value, $field)
    {
        $mysql = Sdp::getModel('core/connection_Mysql');
        $field = (is_null($field)) ? $this->getPrimaryKey() : $field;
        $query = "Select * from {$this->getTableName()} where {$field} = {$value}";
        $data = $mysql->fetchOne($query);
        return $data;
    }
}
