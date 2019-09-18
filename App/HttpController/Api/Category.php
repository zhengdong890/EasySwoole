<?php

namespace App\HttpController\Api;

/**
 * Class Index. 
 * @package App\HttpController
 */
class Category extends Base
{
    public function index() {
        $config = \Yaconf::get("category.cats");
        return $this->writeJson(200, 'OK', $config);
    }
}
