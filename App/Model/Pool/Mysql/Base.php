<?php
namespace App\Model\Pool\Mysql;

use App\Lib\Pool\MysqlObject;
use App\Lib\Pool\MysqlPool;
use EasySwoole\Component\Pool\PoolManager;

class Base
{
    public $db;
    public function __construct()
    {
        $timeout = \Yaconf::get("mysql.POOL_TIME_OUT");
        $mysqlObject = PoolManager::getInstance()->getPool(MysqlPool::class)->getObj($timeout);
        // 类型的判定
        if ($mysqlObject instanceof MysqlObject) {
            $this->db = $mysqlObject;
        } else {
            throw new \Exception('Mysql Pool is error');
        }
    }
    public function __destruct()
    {
        if ($this->db instanceof MysqlObject) {
            PoolManager::getInstance()->getPool(MysqlPool::class)->recycleObj($this->db);
            // 请注意 此处db是该链接对象的引用 即使操作了回收 仍然能访问
            // 安全起见 请一定记得设置为null 避免再次使用导致不可预知的问题
            $this->db = null;
        }
    }

    /**
     * 通过ID 获取 基本信息
     *
     * @param [type] $id
     * @return void
     */
    public function getById($id) {
        $id = intval($id);
        if(empty($id)) {
            return [];
        }

        $this->db->where ("id", $id);
        $result = $this->db->getOne($this->tableName);
        return $result ?? [];
    }
    
}