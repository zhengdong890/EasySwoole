<?php

namespace App\HttpController;

use EasySwoole\Http\AbstractInterface\Controller;

/**
 * Class Index. 
 * @package App\HttpController
 */
class Category extends Controller
{
    /**
     * 首页方法
     * @author : evalor <master@evalor.cn>
     */
    public function index()
    {
        
        $video = [
            'id' => 1,
            'name' => 'singwa老师成功荣获国家级计算机一等奖'
            
        ];
        return $this->writeJson(200, '请求成功', $video);
        
    }
}
