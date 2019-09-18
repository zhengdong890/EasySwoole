<?php
/**
 * Created by PhpStorm.
 * User: yf
 * Date: 2018-12-27
 * Time: 14:35
 */
namespace EasySwoole\Actor\Test;

class RoomActor extends \EasySwoole\Actor\AbstractActor
{

    static function configure(\EasySwoole\Actor\ActorConfig $actorConfig)
    {
        // TODO: Implement configure() method.
        $actorConfig->setActorName('RoomActor');
    }

    function onStart($arg)
    {
        // TODO: Implement onStart() method.
        var_dump('actorId :'.$this->actorId().' start');
    }

    function onMessage($msg)
    {
        // TODO: Implement onMessage() method.
        var_dump('actorId :'.$this->actorId().' recv '.$msg);
        //如果需要回复，则
        return 'hello from '.$this->actorId();
    }

    function onExit()
    {
        // TODO: Implement onExit() method.
        var_dump('actorId :'.$this->actorId().' exit');
    }

    protected function onException(\Throwable $throwable)
    {
        // TODO: Implement onException() method.
    }
}