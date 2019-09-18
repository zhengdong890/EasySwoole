<?php
namespace App\Model;
class Video extends Base{
	public $tableName = "video";

	/**
	 * 通过条件获取数据库里面的video
	 * @auth   singwa
	 * @param  array   $condition [description]
	 * @param  integer $page      [description]
	 * @param  integer $size      [description]
	 * @return [type]             [description]
	 */
	public function getVideoData($condition = [], $page = 1, $size =10) {

		if(!empty($condition['cat_id'])) {
			$this->db->where("cat_id", $condition['cat_id']);
		}
		// 获取正常的内容
		$this->db->where("status", 1);
		if(!empty($size)) {
			$this->db->pageLimit = $size;
		}

		$this->db->orderBy("id", "desc");
		$res = $this->db->paginate($this->tableName, $page);
		//echo $this->db->getLastQuery();
		
		$data = [
			'total_page' => $this->db->totalPages,
			'page_size' => $size,
			'count' => intval($this->db->totalCount),
			'lists' => $res,

		];
		return $data;

	}

	/**
	 * [getVideoCacheData description]
	 * @auth   singwa
	 * @param  array   $condition [description]
	 * @param  integer $size      [description]
	 * @return [type]             [description]
	 */
	public function getVideoCacheData($condition = [], $size = 1000) {

		if(!empty($condition['cat_id'])) {
			$this->db->where("cat_id", $condition['cat_id']);
		}
		// 获取正常的内容
		$this->db->where("status", 1);
		if(!empty($size)) {
			$this->db->pageLimit = $size;
		}

		$this->db->orderBy("id", "desc");
		$res = $this->db->paginate($this->tableName, 1);
		//echo $this->db->getLastQuery();
		return $res;

	}
}