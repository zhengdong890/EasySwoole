<?php
/**
 * Created by PhpStorm.
 * User: yf
 * Date: 2018/5/28
 * Time: 下午6:33
 */

namespace EasySwoole\EasySwoole;


use EasySwoole\EasySwoole\Swoole\EventRegister;
use EasySwoole\EasySwoole\AbstractInterface\Event;
use EasySwoole\Http\Request;
use EasySwoole\Http\Response;

// @1
use EasySwoole\Component\Di;
use App\Lib\Redis\Redis;
use App\Lib\Process\Consumer;
//use EasySwoole\Swoole\Process\ProcessManager;
use App\Lib\Cache\Video as videoCache;
//use EasySwoole\Swoole\Time\Timer;
use EasySwoole\Component\Timer;
use EasySwoole\EasySwoole\ServerManager;
use App\Lib\Process\ConsumerTest;

use App\Lib\Pool\MysqlPool;
use EasySwoole\Component\Pool\PoolManager;

class EasySwooleEvent implements Event
{

    public static function initialize()
    {
        // TODO: Implement initialize() method.
        date_default_timezone_set('Asia/Shanghai');
        $mysqlConfg = \Yaconf::get("mysql");
        // 注册mysql数据库连接池
        PoolManager::getInstance()->register(MysqlPool::class, $mysqlConfg['POOL_MAX_NUM']);
    }

    public static function mainServerCreate(EventRegister $register)
    {
        // TODO: Implement mainServerCreate() method.
        // Mysql 相关  
        // mysql 配置 小伙伴 放到 ini文件
        // 之前es2 - mysql 需要小伙伴 用老师讲解的协程链接池 去优化
        Di::getInstance()->set('MYSQL',\MysqliDb::class,Array (
            'host' => '127.0.0.1',
            'username' => 'root',   
            'password' => '123456',
            'db'=> 'imooc_video',
            'port' => 3306,
            'charset' => 'utf8')    
        );
        Di::getInstance()->set('REDIS', Redis::getInstance());
        Di::getInstance()->set("ES", \App\Model\Es\EsClient::getInstance());

        $allNum = 3;
        for ($i = 0 ;$i < $allNum; $i++){
            //ProcessManager::getInstance()->addProcess("imooc_consumer_testp_{$i}", ConsumerTest::class);
            //ServerManager::getInstance()->getSwooleServer()->addProcess((new ConsumerTest("imooc_consumer_testp_{$i}"))->getProcess());
        }
        $cacheVideoObj = new videoCache();

        //CronTab::getInstance()
        //    ->addRule("test_singwa_crontab", "*/1 * * * *", //function() use($cacheVideoObj) {
        //        $cacheVideoObj->setIndexVideo();
        //    })

        //*/

        $register->add(EventRegister::onWorkerStart, function(\swoole_server $server, $workerId) use($cacheVideoObj){
            if($workerId == 0) {
                // Timer::loop
                Timer::getInstance()->loop(1000*2, function() use($cacheVideoObj) {
                    $cacheVideoObj->setIndexVideo();
                });
                // todo

            }
        });
    }

    public static function onRequest(Request $request, Response $response): bool
    {
        // TODO: Implement onRequest() method.
        return true;
    }

    public static function afterRequest(Request $request, Response $response): void
    {
        // TODO: Implement afterAction() method.
    }

    public static function onReceive(\swoole_server $server, int $fd, int $reactor_id, string $data):void
    {

    }

}