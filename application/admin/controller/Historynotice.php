<?php
namespace app\admin\controller;
use think\Controller;
use \app\api\model\Notice as UserModel;

class Historynotice extends Controller{

    public function index($edit){
        $ctrl =new \app\api\controller\Notice();
        $notice = $ctrl->get_all_notice();
        $count = $ctrl->count_all();
        $this->assign("notice",$notice);
        $this->assign("count",$count);
        $this->assign("edit",$edit);
        return $this->fetch("Historynotice/lishigonggao");
    }
}