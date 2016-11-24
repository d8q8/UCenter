<?php
/**
 * Created by PhpStorm.
 * User: d8q8
 * Date: 2016/11/21
 * Time: 20:01
 */

namespace Home\Controller;
use Think\Controller;

/**
 * 基类控制器
 * Class BaseController
 * @package Home\Controller
 */
class BaseController extends Controller
{
    public function _initialize()
    {
        //初始化
        $this->init();
    }

    /**
     * 初始化
     */
    protected function init(){
        //初始化代码处理你的逻辑
    }
}