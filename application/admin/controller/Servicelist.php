<?php
namespace app\admin\controller;
use think\Controller;

class Servicelist extends Controller{

    public function index(){
        $ctrl =new \app\api\controller\Servicetype();
        $shop =\app\api\model\Shop::get(['ID'=>1]);
        $servicelist = $ctrl->get_all();
        $count = $ctrl->count_all();
        $this->assign('shop',$shop);
        $this->assign("servicelist",$servicelist);
        $this->assign("count",$count);
        return $this->fetch('Servicelist/service');
    }
}
