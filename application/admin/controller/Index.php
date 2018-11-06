<?php
namespace app\admin\controller;
use think\Controller;
use \app\api\model\admin as UserModel;

class Index extends Controller{

    public function index(){
        //if(isset($_COOKIE['login'])){
        //    $user=new \app\api\controller\admin();
        //    if($user->checkuser($_COOKIE['userid'])){
               // shell_exec('python /wechat_zu_admin/public/static/timer.py');
                return $this->fetch('Index/index');
        //    }
        //}
        //return $this->error('请先登录','index/index/index');
    }
}