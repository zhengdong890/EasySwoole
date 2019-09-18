<?php
namespace App\Model;
use EasySwoole\Component\Di;
class Base {
	public $db = "";
	public function __construct() {
		if(empty($this->tableName)) {
			throw new \Exception("table error");
		}

		$db = Di::getInstance()->get("MYSQL");
		if($db instanceof \MysqliDb) {
			$this->db = $db;
		} else {
			throw new \Exception("db error");
		}
	}

	/**
	 * [add description]
	 * @auth  singwa
	 * @date  2018-10-21T16:38:42+0800
	 * @param [type]                   $data [description]
	 */
	public function add($data) {
		if(empty($data) || !is_array($data)) {
			return false;
		}
		return $this->db->insert($this->tableName, $data);
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