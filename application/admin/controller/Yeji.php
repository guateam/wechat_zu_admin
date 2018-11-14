<?php
namespace app\admin\controller;
use think\Controller;

class Yeji extends Controller{

    public function index($edit){
        $ctrl = new \app\api\controller\Technician();
        $yeji =$ctrl->get_all_yeji();
        $this->assign("yeji",$yeji);
        $this->assign("count",count($yeji));
        return $this->fetch('Yeji/Yeji');
    }
}