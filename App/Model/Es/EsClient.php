<?php
namespace App\Model\Es;
//use EasySwoole\Core\AbstractInterface\Singleton;
use EasySwoole\Component\Singleton;
use Elasticsearch\ClientBuilder;
class EsClient {
	use Singleton;
	public $esClient = null;
	private function __construct() {
		$config = \Yaconf::get("es");
		try {
			$this->esClient = ClientBuilder::create()->setHosts([$config['host'] . ":" . $config['port']])->build();
		}catch(\Exception $e) {
			// todo
		}

		if(empty($this->esClient)) {
			// todo
		}
	}

	/**
	 * 当类中不存在该方法时候，直接调用call 实现调用底层redis相关的方法
	 * @auth   singwa
	 * @param  [type] $name      [description]
	 * @param  [type] $arguments [description]
	 * @return [type]            [description]
	 */
	public function __call($name, $arguments) {
		
		///var_dump(...$arguments);
		return $this->esClient->$name(...$arguments);
	}
}