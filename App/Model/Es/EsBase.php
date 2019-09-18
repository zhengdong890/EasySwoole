<?php
namespace App\Model\Es;
use EasySwoole\Component\Di;
class EsBase {
	public $esClient = null;
	public function __construct() {
		$this->esClient = Di::getInstance()->get("ES");
	}

	/**
	 * searchByName
	 * @auth   singwa
	 * @param  [string] $name [description]
	 * @param  [int] $from [description]
	 * @param  [int] $size [description]
	 * @param  string $type [description]
	 * @return [type]       [description]
	 */
	public function searchByName($name, $from =0, $size = 10, $type = "match") {
		$name = trim($name);
		if(empty($name)) {
			return [];
		}
		$params = [
            "index" => $this->index,
            "type" => $this->type,
            'body' => [
                'query' => [
                    $type => [
                        'name' => $name
                    ], 
                ],
                'from' => $from,
                'size' => $size
            ],
        ];

        $result = $this->esClient->search($params);
        return $result;

	}
}