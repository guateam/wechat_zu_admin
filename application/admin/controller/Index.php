<?php
namespace app\admin\controller;
use think\Controller;
use \app\api\model\admin as UserModel;
/**
 * 工作情况页面
 */
class Index extends Controller{
    /**
     * 全局工作情况页面
     */
    public function index(){
        //if(isset($_COOKIE['login'])){
        //    $user=new \app\api\controller\admin();
        //    if($user->checkuser($_COOKIE['userid'])){
                return $this->fetch('index');
        //    }
        //}
        //return $this->error('请先登录','index/index/index');
    }
}