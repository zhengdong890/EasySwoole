<?php

namespace App\HttpController\Api;
use App\Model\Video as VideoModel;
use EasySwoole\Http\Message\Status;

//use EasySwoole\Utility\Validate\Rules;
//use EasySwoole\Utility\Validate\Rule;

use EasySwoole\Validate\Validate;
//use EasySwoole\Component\Logger;
use EasySwoole\EasySwoole\Logger;
//use EasySwoole\Swoole\Task\TaskManager;
use EasySwoole\EasySwoole\Swoole\Task\TaskManager;
use EasySwoole\Component\Di;
/**
 * Class Index. 
 * @package App\HttpController
 */
class Video extends Base
{
    public $logType = "video:";

    /**
     * 视频播放页面基本信息接口
     *
     * @return void
     */
    public function index() {
        $id = intval($this->params['id']);
        if(empty($id)) {
            return $this->writeJson(Status::CODE_BAD_REQUEST, "请求不合法");
        }

        // 获取视频的基本信息
        try {
            $video = (new VideoModel())->getById($id);
        }catch(\Exception $e) {
            // 记录日志 $e->getMessage()
            return $this->writeJson(Status::CODE_BAD_REQUEST, "请求不合法");
        }
        if(!$video || $video['status'] != \Yaconf::get("status.normal")) {
            return $this->writeJson(Status::CODE_BAD_REQUEST, "该视频不存在");
        }
        $video['video_duration'] = gmstrftime("%H:%M:%S", $video['video_duration']);

        // 播放数统计逻辑
        // 投放task异步任务 
        TaskManager::async(function() use($id) {
            // 逻辑
            //sleep(10);
            // redis 

            $res = Di::getInstance()->get("REDIS")->zincrby(\Yaconf::get("redis.video_play_key"), 1, $id);

            // 按天记录 
        });
        
        return $this->writeJson(200, 'OK', $video);
    }

    /**
     * 排行接口， 总排行 今日 本周排行 月排行
     * @auth   singwa
     * @return [type] [description]
     */
    public function rank() {
        $result = Di::getInstance()->get("REDIS")->zrevrange(\Yaconf::get("redis.video_play_key"), 0, -1, "withscores");
        // 留给大家一个作业， 数据完善下
        return $this->writeJson(200, 'OK', $result);
    }

    public function love() {
        $videoId = intval($this->params['videoId']);
        if(empty($videoId)) {
            return $this->writeJson(Status::CODE_BAD_REQUEST, "参数不合法");
        }
        // 跟进这个视频id查询是否存在该视频  untodo
        // 
        // 
        $res = Di::getInstance()->get("REDIS")->zincrby(\Yaconf::get("redis.video_love"), 1, $videoId);

    }

    public function add() {
        $params = $this->request()->getRequestParam();
        Logger::getInstance()->log($this->logType . "add:" .json_encode($params));

        // es3的 数据 校验需要小伙伴自行完成。
        //数据检验
        $ruleObj = new Rules();
        $ruleObj->add('name', "视频名称错误")->withRule(Rule::REQUIRED)->withRule(Rule::MIN_LEN, 2)->withRule(Rule::MAX_LEN, 20);
        $ruleObj->add('url', "视频地址错误")->withRule(Rule::REQUIRED);
        $ruleObj->add('image', "图片地址错误")->withRule(Rule::REQUIRED);
        $ruleObj->add('content', "视频描述错误")->withRule(Rule::REQUIRED);
        $ruleObj->add('cat_id', "栏目ID错误")->withRule(Rule::REQUIRED);

        $validata = $this->validateParams($ruleObj);
        if($validata->hasError()) {
            //print_r($validata->getErrorList());
            return $this->writeJson(Status::CODE_BAD_REQUEST, $validata->getErrorList()->first()->getMessage());
        }
        
        $data = [
            'name' => $params['name'],
            'url' => $params['url'],
            'image' => $params['image'],
            'content' => $params['content'],
            'cat_id' => intval($params['cat_id']),
            'create_time' => time(),
            'uploader' => 'singwa',
            'status' => \Yaconf::get("status.normal"), // 0  1 2
        ];

        // 插入
        try {
            $modelObj = new VideoModel();
            $videoId = $modelObj->add($data);
        }catch(\Exception $e) {
            return $this->writeJson(Status::CODE_BAD_REQUEST, $e->getMessage());
        }

        if(!empty($videoId)) {
                return $this->writeJson(Status::CODE_OK, 'OK', ['id' => $videoId ]);
        } else {
            return $this->writeJson(Status::CODE_BAD_REQUEST, '提交视频有误', ['id' => 0 ]);
        }

    }
}
