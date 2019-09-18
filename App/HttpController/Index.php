<?php

namespace App\HttpController;

use EasySwoole\Http\AbstractInterface\Controller;
use App\Lib\AliyunSdk\AliVod;
use Elasticsearch\ClientBuilder;
/**
 * Class Index. 
 * @package App\HttpController
 */
class Index extends Controller
{
    /**
     * 首页方法
     * @author : evalor <master@evalor.cn>
     */
    function index()
    {   
        $cache = \EasySwoole\FastCache\Cache::getInstance();
        $cache->set('name','仙士可');
        return $this->writeJson(200, "OK",  $cache->keys());
        // 测试 php-elasticsearch demo
        $params = [
            "index" => "imooc_video",
            "type" => "video",
            //"id" => 1,
            'body' => [

                'query' => [
                    'match' => [
                        'name' => '刘德华'
                    ], 
                ],
            ],
        ];

        $client = ClientBuilder::create()->setHosts(["127.0.0.1:8301"])->build();
        $result = $client->search($params);

        return $this->writeJson(200, "OK", $result);

    }

    public function testali() {
        $obj = new AliVod();
        $title = "singwa-imooc-video";
        $videoName = "1.mp4";
        $result = $obj->createUploadVideo($title, $videoName);

        $uploadAddress = json_decode(base64_decode($result->UploadAddress), true);

        $uploadAuth = json_decode(base64_decode($result->UploadAuth), true);

        $obj->initOssClient($uploadAuth, $uploadAddress);

        $videoFile = "/home/work/hdtocs/imooc/imooc_esapi/webroot/video/2018/10/7648e6280470bbbc.mp4";
        $result = $obj->uploadLocalFile($uploadAddress, $videoFile);
        print_r($result);
    }

    public function getVideo() {
        $videoId = "345183ba6d54420080ae63830afb663c";
        $obj = new AliVod();
        print_r($obj->getPlayInfo($videoId));
    }
}
