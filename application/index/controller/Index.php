<?php
namespace app\index\controller;
use think\Controller;
/**
 * 登录主页
 */
class Index extends Controller{
    public function index(){
        return $this->fetch();
    }
}