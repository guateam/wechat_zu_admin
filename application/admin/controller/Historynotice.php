<?php
namespace app\admin\controller;
use think\Controller;
use \app\api\model\Notice as UserModel;

class Historynotice extends Controller{

    public function index(){
        $ctrl =new \app\api\controller\Notice();
        $notice = $ctrl->get_all_notice();
        $count = $ctrl->count_all();
        $this->assign("notice",$notice);
        $this->assign("count",$count);
        return $this->fetch("Historynotice/lishigonggao");
    }
}