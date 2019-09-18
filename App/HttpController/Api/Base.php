<?php

namespace App\HttpController\Api;

use EasySwoole\Http\AbstractInterface\Controller;

/**
 * Api模块下的基础类库
 * Class Index. 
 * @package App\HttpController
 */
class Base extends Controller
{

    /**
     * 放一些请求的参数数据
     * @var array
     */
    public $params = [];
    public function index() {
        //return $this->writeJson(201, 'OK', ['aas']);
    }

    /**
     * 权限相关
     * @auth   singwa
     * @date   2018-10-03T18:00:40+0800
     * @param  [type]
     * @return [type]
     */
    public function onRequest($action):?bool {

        $this->getParams();
        return true;
    }


    /**
     * 获取 params
     * @auth   singwa
     * @return [type] [description]
     */
    public function getParams() {
        $params = $this->request()->getRequestParam();
        $params['page'] = !empty($params['page']) ? intval($params['page']) : 1;
        $params['size'] = !empty($params['size']) ? intval($params['size']) : 5;

        $params['from'] = ($params['page'] - 1) * $params['size'];

        $this->params = $params;
    }
    /**
     * [getPagingDatas description]
     * @auth   singwa
     * @param  [type] $count [description]
     * @param  [type] $data  [description]
     * @param  [int] $isSplice  [是否切割]
     * @return [type]        [description]
     */
    public function getPagingDatas($count, $data, $isSplice = 1) {
        $totalPage = ceil($count / $this->params['size']);
        $maxPageSize = \Yaconf::get("base.maxPageSize");
        if($totalPage > $maxPageSize) {
            $totalPage = $maxPageSize;
        }
        $data = $data ?? [];

        if($isSplice == 1) {
            $data = array_splice($data, $this->params['from'], $this->params['size']);
        }

        return [
            'total_page' => $totalPage,
            'page_size' => $this->params['page'],
            'count' => intval($count),
            'lists' => $data,
        ];
    }
    /**
     * @auth   singwa
     * @date   2018-10-03T18:04:20+0800
     * @param  \Throwable
     * @param  [type]
     * @return [type]
     */
    /*public function onException(\Throwable $throwable,$actionName):void {
        $this->writeJson(400, '请求不合法');
    }*/

    /**
     * json数据格式输出
     *@statusCode
     */
    protected function writeJson($statusCode = 200, $message = null, $result = null){
        if(!$this->response()->isEndResponse()){
            $data = Array(
                "code"=>$statusCode,
                "message"=>$message,
                "result"=>$result
            );
            $this->response()->write(json_encode($data,JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES));
            $this->response()->withHeader('Content-type','application/json;charset=utf-8');
            $this->response()->withStatus($statusCode);
            return true;
        }else{
            trigger_error("response has end");
            return false;
        }
    }
    
}
