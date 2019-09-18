<?php
/**
 * Created by PhpStorm.
 * User: yf
 * Date: 2018-12-27
 * Time: 12:13
 */

namespace EasySwoole\Actor;


use Swoole\Coroutine\Channel;

abstract class AbstractActor
{
    private $hasDoExit = false;
    private $actorId;
    private $arg;
    private $channel;
    private $tickList = [];

    final function __construct(string $actorId,$arg)
    {
        $this->actorId = $actorId;
        $this->arg = $arg;
        $this->channel = new Channel(16);
    }

    abstract static function configure(ActorConfig $actorConfig);
    abstract function onStart($arg);
    abstract function onMessage($msg);
    abstract function onExit();
    function actorId()
    {
        return $this->actorId;
    }

    /*
     * 请用该方法来添加定时器，方便退出的时候自动清理定时器
     */
    function tick($time,callable $callback)
    {
        $id = swoole_timer_tick($time,$callback);
        $this->tickList[$id] = $id;
    }

    function deleteTick(int $timerId)
    {
        unset($this->tickList[$timerId]);
        return swoole_timer_clear($timerId);
    }

    function getArs()
    {
        return $this->arg;
    }

    function getChannel():Channel
    {
        return $this->channel;
    }

    function __run()
    {
        try{
            $this->onStart($this->arg);
        }catch (\Throwable $throwable){
            $this->onException($throwable);
        }
        while (!$this->hasDoExit){
            $array = $this->channel->pop(0.1);
            if(!empty($array)){
                $msg = $array['msg'];
                if($msg == 'exit'){
                    $reply = $this->exitHandler();
                }else{
                    $reply = $this->onMessage($msg);
                }
                if($array['reply']){
                    $conn = $array['connection'];
                    fwrite($conn,Protocol::pack(serialize($reply)));
                    fclose($conn);
                }
            }
        }
    }

    /*
     * 一个actor可以自杀
     */
    protected function exit()
    {
        $this->channel->push([
            'msg'=>'exit',
            'reply'=>false
        ]);
    }

    private function exitHandler()
    {
        $reply = null;
        try{
            //清理定时器
            foreach ($this->tickList as $tickId){
                swoole_timer_clear($tickId);
            }
            $this->hasDoExit = true;
            $this->channel->close();
            $reply = $this->onExit();
            if($reply === null){
                $reply = true;
            }
        }catch (\Throwable $throwable){
            $this->onException($throwable);
        }
        return $reply;
    }

    abstract protected function onException(\Throwable $throwable);

    public static function invoke():?ActorClient
    {
        return Actor::getInstance()->client(static::class);
    }
}