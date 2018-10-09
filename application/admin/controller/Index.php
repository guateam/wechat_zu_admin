<?php
namespace app\admin\controller;
use think\Controller;
use \app\api\model\admin as UserModel;

class Index extends Controller{

    public function index(){
        //if(isset($_COOKIE['login'])){
        //    $user=new \app\api\controller\admin();
        //    if($user->checkuser($_COOKIE['userid'])){
                return $this->fetch('Index/Index');
        //    }
        //}
        //return $this->error('请先登录','index/index/index');
    }
}