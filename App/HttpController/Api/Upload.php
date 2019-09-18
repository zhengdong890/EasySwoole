<?php

namespace App\HttpController\Api;

use App\HttpController\Api\Base;
use EasySwoole\Component\Di;
use App\Lib\Upload\Video;
use App\Lib\ClassArr;
/**
 * 文件上传逻辑 - 视频 图片
 * @package App\HttpController
 */
class Upload extends Base
{

    public function file() {
        
        $request = $this->request();
        $files = $request->getSwooleRequest()->files;
        $types = array_keys($files);
        $type = $types[0];
        if(empty($type)) {
            return $this->writeJson(400, '上传文件不合法');
        }


        // 新的写法
        //$obj = "\App\Lib\Upload\". $type;
        //
        // PHP 反射机制
         
        try {
            //$obj = new Video($request);
            //$obj = new $obj($request);
            //
            $classObj = new ClassArr();
            $classStats = $classObj->uploadClassStat();
            $uploadObj = $classObj->initClass($type, $classStats, [$request, $type]);
            $file = $uploadObj->upload();
        }catch(\Exception $e) {
            return $this->writeJson(400, $e->getMessage(), []);
        }
        if(empty($file)) {
            return $this->writeJson(400, "上传失败", []);
        }

        $data = [
            'url' => $file,
        ];
        return $this->writeJson(200, "OK", $data);


        /*$request = $this->request();
        $videos = $request->getUploadedFile("file");
        

        $flag = $videos->moveTo("/home/work/hdtocs/imooc/imooc_esapi/webroot/1.mp4");
        $data = [
            'url' => "/1.mp4",
            'flag' => $flag
        ];
        if($flag) {
            return $this->writeJson(200, 'OK', $data);
        } else {
            return $this->writeJson(400, 'OK', $data);
        }*/
    }

}
